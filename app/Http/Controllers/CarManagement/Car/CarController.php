<?php

namespace App\Http\Controllers\CarManagement\Car;

use App\Models\Car;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CarController extends Controller
{
    // 🟢 List all cars
    public function index(Request $request)
{
    $query = Car::query();

    if ($request->has('company')) {
        $query->where('company', $request->company);
    }

    return response()->json(['success' => true, 'data' => $query->get()]);
}


    // 🟢 Store new car
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|digits:4|integer|min:1900|max:' . (date('Y') + 1),
        ]);

        $car = Car::create($validated);

        return response()->json(['success' => true, 'data' => $car, 'message' => 'Car added successfully']);
    }

    // 🟢 Show for editing
    public function show($id)
    {
        $car = Car::findOrFail($id);
        return response()->json(['success' => true, 'data' => $car]);
    }

    public function getCarProblems($id)
    {
        $car = Car::with('problems')->find($id);

        if (!$car) {
            return response()->json([
                'status' => false,
                'message' => 'Car not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $car->problems,
        ]);
    }


    // 🟢 Update car
    public function update(Request $request, $id)
    {
        $car = Car::findOrFail($id);

        $validated = $request->validate([
            'company' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|digits:4|integer|min:1900|max:' . (date('Y') + 1),
        ]);

        $car->update($validated);

        return response()->json(['success' => true, 'data' => $car, 'message' => 'Car updated successfully']);
    }

    // 🟢 Delete car
    public function destroy($id)
    {
        $car = Car::findOrFail($id);
        $car->delete();

        return response()->json(['success' => true, 'message' => 'Car deleted successfully']);
    }
    public function getAssignedCarsForExpert()
    {
        $user = Auth::user();

        // Get cars where:
        // - The car is assigned to the expert via car_user
        // - The car's company matches the expert's company
        $cars = $user->cars()->where('company', $user->company)->get();

        return response()->json(['success' => true, 'data' => $cars]);
    }
}
