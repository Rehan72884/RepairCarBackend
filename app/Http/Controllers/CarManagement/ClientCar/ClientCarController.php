<?php

namespace App\Http\Controllers\CarManagement\ClientCar;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientCarController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Auth::user()->myCars()->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'car_id' => 'required|exists:cars,id'
        ]);

        Auth::user()->myCars()->syncWithoutDetaching([$request->car_id]);

        return response()->json(['message' => 'Car added to your list']);
    }


    public function destroy($carId)
    {
        Auth::user()->myCars()->detach($carId);

        return response()->json(['message' => 'Car removed from your list']);
    }
}

