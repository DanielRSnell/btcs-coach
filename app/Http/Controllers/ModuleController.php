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
        if ($user) {
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
                ->where('context', 'LIKE', '%' . $module->slug . '%')
                ->orWhere('context', 'LIKE', '%' . str_replace('-', ' ', $module->title) . '%')
                ->orderBy('priority', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'description' => $item->description,
                        'priority' => $item->priority,
                        'status' => $item->status,
                        'due_date' => $item->due_date,
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
                'role' => $user->role ?? 'member'
            ] : null,
            'actionItems' => $actionItems
        ]);
    }
}