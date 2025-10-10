<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favourite;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $patient = Patient::where('user_id', $user->id)->firstOrFail();

        $favourites = Favourite::with('doctor.user')
            ->where('patient_id', $patient->id)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $favourites
        ]);
    }

    public function store(Request $request, Doctor $doctor)
    {
        $user = Auth::user();
        $patient = Patient::where('user_id', $user->id)->firstOrFail();

        $exists = Favourite::where('patient_id', $patient->id)
            ->where('doctor_id', $doctor->id)
            ->first();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'Doctor already in favourites'
            ], 409);
        }

        $favourite = Favourite::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Doctor added to favourites',
            'data' => $favourite->load('doctor.user')
        ], 201);
    }

    public function destroy(Doctor $doctor)
    {
        $user = Auth::user();
        $patient = Patient::where('user_id', $user->id)->firstOrFail();

        $deleted = Favourite::where('patient_id', $patient->id)
            ->where('doctor_id', $doctor->id)
            ->delete();

        if (!$deleted) {
            return response()->json([
                'status' => false,
                'message' => 'Doctor not found in favourites'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Doctor removed from favourites'
        ]);
    }
}
