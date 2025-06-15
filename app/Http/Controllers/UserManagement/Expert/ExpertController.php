<?php

namespace App\Http\Controllers\UserManagement\Expert;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ExpertController extends Controller
{
    // 🟢 List all users with "expert" role
    public function index()
    {
        $experts = User::role('Expert')->get(); // from Spatie
        return response()->json(['success' => true, 'data' => $experts]);
    }

    // 🟢 Store new expert
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // assign expert role
        $user->assignRole('Expert');

        return response()->json(['success' => true, 'data' => $user]);
    }

    // 🟢 Show expert by ID
    public function show($id)
    {
        $expert = User::role('Expert')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $expert]);
    }

    // 🟢 Update expert
    public function update(Request $request, $id)
    {
        $expert = User::role('Expert')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $expert->id,
            'password' => 'nullable|string|min:6',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $expert->update($validated);

        return response()->json(['success' => true, 'data' => $expert]);
    }

    // 🟢 Delete expert
    public function destroy($id)
    {
        $expert = User::role('Expert')->findOrFail($id);
        $expert->delete();

        return response()->json(['success' => true, 'message' => 'Expert deleted']);
    }
}
