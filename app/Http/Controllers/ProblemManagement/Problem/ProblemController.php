<?php

namespace App\Http\Controllers\ProblemManagement\Problem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Problem;
use App\Models\Car;

class ProblemController extends Controller
{
    public function index()
    {
        $problems = Problem::with('car')->latest()->get();
        return response()->json(['success' => true, 'data' => $problems]);
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
}
