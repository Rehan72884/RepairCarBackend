<?php

namespace App\Http\Controllers\UserManagement\Expert;

use App\Models\User;
use App\Enums\CompanyEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Enum;


class ExpertController extends Controller
{
    // 🟢 List all users with "expert" role
    public function index()
    {
        $experts = User::role('Expert')->get(); // from Spatie
        return response()->json(['success' => true, 'data' => $experts]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'company' => ['nullable', new Enum(CompanyEnum::class)],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'company' => $validated['company'] ?? null,
        ]);

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
            'company' => ['nullable', new Enum(CompanyEnum::class)],
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
    public function companyList()
    {
        $companies = array_map(fn($case) => $case->value, CompanyEnum::cases());

        return response()->json(['success' => true, 'data' => $companies]);
    }
}
