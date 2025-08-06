<?php

namespace App\Http\Controllers;

use App\Models\ActionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActionItemController extends Controller
{
    /**
     * Mark an action item as completed
     */
    public function markAsCompleted(Request $request, ActionItem $actionItem)
    {
        // Ensure the action item belongs to the authenticated user
        if ($actionItem->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to modify this action item.');
        }

        $actionItem->markAsCompleted();

        // If it's an Inertia request, redirect back with success message
        if ($request->header('X-Inertia')) {
            return redirect()->back()->with('success', 'Action item marked as completed');
        }

        // For regular API requests, return JSON
        return response()->json([
            'success' => true,
            'message' => 'Action item marked as completed',
            'action_item' => [
                'id' => $actionItem->id,
                'status' => $actionItem->status,
                'completed_at' => $actionItem->completed_at?->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * Update action item status
     */
    public function updateStatus(Request $request, ActionItem $actionItem)
    {
        // Ensure the action item belongs to the authenticated user
        if ($actionItem->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to modify this action item.');
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled'
        ]);

        $actionItem->update([
            'status' => $request->status,
            'completed_at' => $request->status === 'completed' ? now() : null,
        ]);

        // If it's an Inertia request, redirect back with success message
        if ($request->header('X-Inertia')) {
            return redirect()->back()->with('success', 'Action item status updated');
        }

        // For regular API requests, return JSON
        return response()->json([
            'success' => true,
            'message' => 'Action item status updated',
            'action_item' => [
                'id' => $actionItem->id,
                'status' => $actionItem->status,
                'completed_at' => $actionItem->completed_at?->format('Y-m-d H:i:s'),
            ]
        ]);
    }
}
