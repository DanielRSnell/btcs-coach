<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Module::where('is_active', true)
            ->withCount('users')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return Inertia::render('modules', [
            'modules' => $modules
        ]);
    }

    public function show(Module $module)
    {
        if (!$module->is_active) {
            abort(404);
        }

        $module->load(['users' => function($query) {
            $query->select('users.id', 'users.name', 'users.email')
                  ->withPivot(['assigned_at', 'completed_at', 'progress_data']);
        }]);

        return Inertia::render('module-detail', [
            'module' => $module
        ]);
    }

    public function chat(Module $module)
    {
        if (!$module->is_active) {
            abort(404);
        }

        // Track that user started this module session
        $user = auth()->user();
        
        // Load PI behavioral pattern relationship if user exists
        if ($user) {
            $user->load('piBehavioralPattern');
            $user->accessibleModules()->syncWithoutDetaching([
                $module->id => [
                    'assigned_at' => now(),
                    'progress_data' => json_encode(['session_started' => now()])
                ]
            ]);
        }

        // Get action items related to this module for the current user
        $actionItems = [];
        if ($user) {
            $actionItems = $user->actionItems()
                ->where('module_id', $module->id)
                ->orderBy('priority', 'desc')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'description' => $item->description,
                        'priority' => $item->priority,
                        'status' => $item->status,
                        'due_date' => $item->due_date?->format('Y-m-d'),
                        'context' => $item->context,
                    ];
                });
        }

        return Inertia::render('module-chat', [
            'module' => $module,
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? 'member',
                'pi_behavioral_pattern_id' => $user->pi_behavioral_pattern_id,
                'pi_behavioral_pattern' => $user->piBehavioralPattern ? [
                    'id' => $user->piBehavioralPattern->id,
                    'name' => $user->piBehavioralPattern->name,
                    'code' => $user->piBehavioralPattern->code,
                    'description' => $user->piBehavioralPattern->description,
                ] : null,
                'pi_raw_scores' => $user->pi_raw_scores,
                'pi_assessed_at' => $user->pi_assessed_at?->format('Y-m-d'),
                'pi_notes' => $user->pi_notes,
                'pi_profile' => $user->pi_profile,
                'has_pi_assessment' => $user->hasPiAssessment(),
                'has_pi_profile' => $user->hasPiProfile(),
            ] : null,
            'actionItems' => $actionItems
        ]);
    }
}