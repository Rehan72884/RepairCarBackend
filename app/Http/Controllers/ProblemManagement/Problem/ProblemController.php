<?php

namespace App\Http\Controllers\ProblemManagement\Problem;

use App\Models\Car;
use App\Models\User;
use App\Models\Problem;
use Illuminate\Http\Request;
use App\Models\ClientProblem;
use App\Http\Controllers\Controller;
use App\Notifications\ProblemAssignedToExpert;

class ProblemController extends Controller
{
    public function index(Request $request)
    {
        $query = Problem::with('car')->latest();

        // 👇 Filter only if car_id is passed
        if ($request->has('car_id')) {
            $query->where('car_id', $request->car_id);
        }

        $problems = $query->get();

        return response()->json([
            'success' => true,
            'data' => $problems,
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        

        $problem = Problem::create($validated);
        return response()->json([
            'success' => true,
            'message' => 'Problem created',
            'problem' => $problem
            
        ]);
    }

    public function show($id)
    {
        $problem = Problem::with('car')->findOrFail($id);
        return response()->json([
            'success' => true,
            'problem' => $problem
        ]);
    }

    public function update(Request $request, $id)
    {
        $problem = Problem::findOrFail($id);

        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $problem->update($validated);
        return response()->json([
            'success' => true,
            'message' => 'Problem updated',
            'problem' => $problem
        
        ]);
    }

    public function destroy($id)
    {
        $problem = Problem::findOrFail($id);
        $problem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Problem deleted'
        ]);
    }

    public function assignProblemToExpert(Request $request)
    {
        $validated = $request->validate([
            'problem_id' => 'required|exists:client_problems,id',
            'expert_id' => 'required|exists:users,id',
        ]);

        $problem = ClientProblem::findOrFail($validated['problem_id']);
        $expert = User::findOrFail($validated['expert_id']);

        $problem->update([
            'assigned_expert_id' => $expert->id,
            'status' => 'assigned',
        ]);

        $expert->notify(new ProblemAssignedToExpert($problem));

        return response()->json(['message' => 'Problem assigned to expert']);
    }

    public function getPendingProblems()
    {
        $pendingProblems = ClientProblem::with(['client', 'car'])
            ->where('status', 'pending')
            ->whereNull('assigned_expert_id')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pendingProblems,
        ]);
    }
    
    public function getAssignedProblemsForExpert()
    {
        $user = auth()->user();

        $problems = ClientProblem::with('car')
            ->where('assigned_expert_id', $user->id)
            ->where('status', 'assigned')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $problems,
        ]);
    }
}
