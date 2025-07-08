<?php

namespace App\Http\Controllers\UserManagement\Expert;

use App\Models\User;
use App\Enums\CompanyEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ExpertController extends Controller
{
    // 🟢 List all experts
    public function index()
    {
        $experts = User::role('Expert')->with('cars')->get();
        return response()->json(['success' => true, 'data' => $experts]);
    }

    // 🟢 Store new expert
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'company' => ['nullable', 'string', 'in:' . implode(',', array_column(CompanyEnum::cases(), 'value'))],
                'car_ids' => 'nullable|array',
                'car_ids.*' => 'exists:cars,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $e->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'company' => $validated['company'] ?? null,
        ]);

        $user->assignRole('Expert');

        if (!empty($validated['car_ids'])) {
            $user->cars()->sync($validated['car_ids']);
        }

        return response()->json(['success' => true, 'data' => $user->load('cars')]);
    }

    // 🟢 Show expert by ID
    public function show($id)
    {
        $expert = User::role('Expert')->with('cars')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $expert]);
    }

    // 🟢 Update expert
    public function update(Request $request, $id)
    {
        $expert = User::role('Expert')->findOrFail($id);

        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:users,email,' . $expert->id,
                'password' => 'nullable|string|min:6',
                'company' => ['nullable', 'string', 'in:' . implode(',', array_column(CompanyEnum::cases(), 'value'))],
                'car_ids' => 'nullable|array',
                'car_ids.*' => 'exists:cars,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $e->errors(),
            ], 422);
        }

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $expert->update($validated);

        if (isset($validated['car_ids'])) {
            $expert->cars()->sync($validated['car_ids']);
        }

        return response()->json(['success' => true, 'data' => $expert->load('cars')]);
    }

    // 🟢 Delete expert
    public function destroy($id)
    {
        $expert = User::role('Expert')->findOrFail($id);
        $expert->delete();

        return response()->json(['success' => true, 'message' => 'Expert deleted']);
    }

    // 🟢 List company options
    public function companyList()
    {
        $companies = array_map(fn($case) => $case->value, CompanyEnum::cases());
        return response()->json(['success' => true, 'data' => $companies]);
    }
}
