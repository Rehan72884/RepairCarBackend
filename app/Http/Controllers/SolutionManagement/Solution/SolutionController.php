<?php

namespace App\Http\Controllers\SolutionManagement\Solution;

use App\Models\Problem;
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

        if (!$user->hasRole('Expert')) {
            return response()->json(['message' => 'Only experts can create solutions'], 403);
        }

        $validated = $request->validate([
            'problem_id' => 'required|exists:problems,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $problem = Problem::with('car')->findOrFail($validated['problem_id']);

        if (!$problem->car) {
            return response()->json(['message' => 'Problem has no associated car'], 400);
        }

        if ($user->company->value !== $problem->car->company) {
            return response()->json([
                'message' => "Unauthorized: You can only add solutions for {$user->company->value} cars"
            ], 403);
        }

        $validated['expert_id'] = $user->id;

        $solution = Solution::create($validated);
        return response()->json(['message' => 'Solution created', 'data' => $solution]);
    }

    public function update(Request $request, $id)
    {
        $solution = Solution::with('problem.car')->findOrFail($id);
        $user = auth()->user();

        if (!$user->hasRole('Expert') || $solution->expert_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $carCompany = optional($solution->problem->car)->company;

        if ($user->company !== $carCompany) {
            return response()->json([
                'message' => "Unauthorized: You can only update solutions for {$user->company->value} cars"
            ], 403);
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
