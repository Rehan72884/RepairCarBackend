<?php

namespace App\Http\Controllers\SolutionManagement\SolutionFeedback;

use App\Models\SolutionFeedback;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SolutionFeedbackController extends Controller
{
    // Store or update feedback (Upsert)
    public function store(Request $request)
    {
        $request->validate([
            'solution_id' => 'required|exists:solutions,id',
            'liked' => 'nullable|boolean',
            'rating' => 'nullable|integer|min:1|max:5',
            'feedback' => 'nullable|string',
        ]);

        $feedback = SolutionFeedback::updateOrCreate(
            [
                'solution_id' => $request->solution_id,
                'user_id' => auth()->id()
            ],
            $request->only('liked', 'rating', 'feedback')
        );

        return response()->json(['message' => 'Feedback submitted', 'data' => $feedback]);
    }

    // Get feedback for a solution
    public function getBySolution($solutionId)
    {
        return SolutionFeedback::with('user')
            ->where('solution_id', $solutionId)
            ->get();
    }

    // Update feedback by ID
    public function update(Request $request, $id)
    {
        $feedback = SolutionFeedback::findOrFail($id);

        // Only the user who created the feedback can update it
        if ($feedback->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'liked' => 'nullable|boolean',
            'rating' => 'nullable|integer|min:1|max:5',
            'feedback' => 'nullable|string',
        ]);

        $feedback->update($request->only('liked', 'rating', 'feedback'));

        return response()->json(['message' => 'Feedback updated', 'data' => $feedback]);
    }

    // Delete feedback by ID
    public function destroy($id)
    {
        $feedback = SolutionFeedback::findOrFail($id);

        // Only the user who created it or admin can delete
        if (!auth()->user()->hasRole('Admin') && $feedback->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $feedback->delete();

        return response()->json(['message' => 'Feedback deleted']);
    }
}
