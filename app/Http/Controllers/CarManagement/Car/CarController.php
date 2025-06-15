<?php

namespace App\Http\Controllers\CarManagement\Car;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    // 🟢 List all cars
    public function index()
    {
        $cars = Car::all();
        return response()->json(['success' => true, 'data' => $cars]);
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
}
