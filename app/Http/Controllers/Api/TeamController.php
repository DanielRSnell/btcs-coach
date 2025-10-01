<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Get team members for the authenticated user's Org Level 2.
     * Returns all team members from team.json that share the same Org Level 2.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        if (empty($user->org_level_2)) {
            return response()->json([
                'success' => false,
                'message' => 'User does not have an Org Level 2 assigned',
                'team_members' => [],
            ], 200);
        }

        $teamMembers = $user->getMyTeamMembers();

        return response()->json([
            'success' => true,
            'org_level_2' => $user->org_level_2,
            'team_members' => $teamMembers,
            'count' => count($teamMembers),
        ]);
    }

    /**
     * Get team members by a specific Org Level 2.
     * This endpoint allows querying team members by Org Level 2.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function byOrgLevel(Request $request): JsonResponse
    {
        $request->validate([
            'org_level_2' => 'required|string',
        ]);

        $orgLevel2 = $request->input('org_level_2');
        $teamMembers = User::getTeamMembers($orgLevel2);

        return response()->json([
            'success' => true,
            'org_level_2' => $orgLevel2,
            'team_members' => $teamMembers,
            'count' => count($teamMembers),
        ]);
    }
}
