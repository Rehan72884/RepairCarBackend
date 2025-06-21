<?php

namespace App\Http\Controllers\SolutionManagement\Solution;

use App\Models\Solution;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

        // Only experts can store
        if (!$user->hasRole('Expert')) {
            return response()->json(['message' => 'Only experts can create solutions'], 403);
        }

        $validated = $request->validate([
            'problem_id' => 'required|exists:problems,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $validated['expert_id'] = $user->id;

        $solution = Solution::create($validated);
        return response()->json(['message' => 'Solution created', 'data' => $solution]);
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
