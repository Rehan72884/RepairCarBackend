<?php

namespace App\Http\Controllers\StepManagement\Step;

use App\Models\Step;
use App\Models\Solution;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class StepController extends Controller
{
    public function index($solutionId)
    {
        $solution = Solution::findOrFail($solutionId);

        // Only show steps to the solution owner
        if ($solution->expert_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $steps = $solution->steps()->orderBy('order')->get();
        return response()->json(['data' => $steps]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'solution_id' => 'required|exists:solutions,id',
            'description' => 'required|string',
            'order' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $solution = Solution::findOrFail($validated['solution_id']);

        if ($solution->expert_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('steps', 'public');
        }

        $step = $solution->steps()->create($validated);
        return response()->json(['message' => 'Step created', 'data' => $step]);
    }

    public function update(Request $request, $id)
    {
        $step = Step::findOrFail($id);
        $solution = $step->solution;

        if ($solution->expert_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->only('description', 'order');

        if ($request->hasFile('image')) {
            if ($step->image) {
                Storage::disk('public')->delete($step->image);
            }
            $data['image'] = $request->file('image')->store('steps', 'public');
        }

        $step->update($data);
        return response()->json(['message' => 'Step updated']);
    }

    public function destroy($id)
    {
        $step = Step::findOrFail($id);
        $solution = $step->solution;

        if ($solution->expert_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($step->image) {
            Storage::disk('public')->delete($step->image);
        }

        $step->delete();
        return response()->json(['message' => 'Step deleted']);
    }
}
