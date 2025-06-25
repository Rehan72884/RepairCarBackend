<?php

namespace App\Http\Controllers\SolutionManagement\Solution;

use App\Models\User;
use App\Models\Problem;
use App\Models\Solution;
use Illuminate\Http\Request;
use App\Models\ClientProblem;
use App\Http\Controllers\Controller;
use App\Notifications\SolutionAddedByExpert;
use App\Notifications\SolutionReadyForClient;

class SolutionController extends Controller
{
    public function index($problemId)
    {
        $user = auth()->user();

        // Base query with steps
        $query = Solution::with('steps')
            ->where('problem_id', $problemId);

        // Add aggregates for feedback: likes, dislikes, average_rating
        $query->withCount([
            'feedbacks as likes' => function ($q) {
                $q->where('liked', true);
            },
            'feedbacks as dislikes' => function ($q) {
                $q->where('liked', false);
            },
        ])->withAvg('feedbacks as average_rating', 'rating');

        if ($user->hasRole('Admin') || $user->hasRole('Client')) {
            $solutions = $query->get();
        } else {
            // Experts only see their own solutions
            $solutions = $query->where('expert_id', $user->id)->get();
        }

        return $solutions;
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->hasRole('Expert')) {
            return response()->json(['message' => 'Only experts can create solutions'], 403);
        }

        $validated = $request->validate([
            'type' => 'nullable|in:client,default',
            'problem_id' => 'required|integer',
            'title' => 'required|string',
            'description' => 'nullable|string',
        ]);

        // If it's a client problem
        if (($validated['type'] ?? 'default') === 'client') {
            $clientProblem = ClientProblem::findOrFail($validated['problem_id']);

            // Optional: verify expert is assigned to this client problem
            if ($clientProblem->assigned_expert_id !== $user->id) {
                return response()->json(['message' => 'You are not assigned to this client problem'], 403);
            }

            $clientProblem->update(['status' => 'solved']);

            $solution = Solution::create([
                'problem_id' => $clientProblem->id,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'expert_id' => $user->id,
            ]);

            // Notify Admin
            $admins = User::role('Admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new SolutionAddedByExpert($solution));
            }
            $client = $clientProblem->client;
            if ($client) {
                $client->notify(new SolutionReadyForClient($solution));
            }
        }
        // If it's a regular problem
        else {
            $problem = Problem::findOrFail($validated['problem_id']);

            // Optional: check expert's company matches
            if ($problem->car->company !== $user->company) {
                return response()->json(['message' => 'You can only solve problems for your company'], 403);
            }

            $solution = Solution::create([
                'problem_id' => $problem->id,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'expert_id' => $user->id,
            ]);
        }

        return response()->json([
            'message' => 'Solution created',
            'data' => $solution,
        ]);
    }


    public function update(Request $request, $id)
    {
        $solution = Solution::findOrFail($id);
        $user = auth()->user();

        // Only the owning expert can update
        if (!$user->hasRole('Expert') || $solution->expert_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $solution->update($request->only('title', 'description'));
        return response()->json(['message' => 'Solution updated']);
    }

    public function destroy($id)
    {
        $solution = Solution::findOrFail($id);
        $user = auth()->user();

        // Only admin or the owning expert can delete
        if (!$user->hasRole('Admin') && $solution->expert_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $solution->delete();
        return response()->json(['message' => 'Solution deleted']);
    }
}
