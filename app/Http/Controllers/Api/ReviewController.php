<?php

namespace App\Http\Controllers\Api;

use App\Models\Doctor;
use App\Models\Review;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Review::with(['patient.user', 'doctor.user']);

        if ($request->filled('doctor_id')){
            $query->where('doctor_id', $request->input('doctor_id'));
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Reviews retrieved successfully',
            'data' => $reviews
        ], 200);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Doctor $doctor)
    {
        $user = Auth::user();

        if ($user->role !== 'patient'){
            return response()->json([
                'status' => false,
                'message' => 'Only patient can create reviews.'
            ], 403);
        }

        $patient = Patient::where('user_id', $user->id)->first();

        if (!$patient){
            return response()->json([
                'status' => false,
                'message' => 'Patient profile not found.'
        ], 404);
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $existing = Review::where('patient_id', $patient->id)
                            ->where('doctor_id', $doctor->id)
                            ->first();
        if($existing){
            return response()->json([
                'status' => false,
                'message' => 'You have already reviewed this doctor.'
            ], 409);
        }
        $review = Review::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);
        $this->recalculateDoctorRating($doctor);

        return response()->json([
            'status' => true,
            'message' => 'Review submitted successfully',
            'data' => $review->load(['patient.user', 'doctor.user'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        return response()->json([
            'status' => true,
            'data' => $review->load(['patient.user', 'doctor.user'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        $user = Auth::user();

        if ($user->role != 'admin' && $review->patient_id !==  $user->id){
            return response()->json([
                'status' => false,
                'message' => 'Forbidden'
            ], 403);
        }
        $data = $request->validate([
            'rating' => ['sometimes', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);
        $review->fill($data);
        $review->save();
        $this->recalculateDoctorRating($review->doctor);

        return response()->json([
            'status' => true,
            'message' => 'Review submitted successfully',
            'data' => $review->load(['patient.user', 'doctor.user'])
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        $user = Auth::user();
        if ($user->role !== 'admin'){
            return response()->json([
                'status' => false,
                'message' => 'Only admins can delete reviews'
            ], 403);
        }
        $doctor = $review->doctor;
        $review->delete();
        $this->recalculateDoctorRating($doctor);
        return response()->json([
            'status' => true,
            'message' => 'Review deleted'
        ]);
    }

    /**
     * Admin-only: verify a review (set is_verified = true)
     */
    public function verify(Review $review)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            return response()->json(['status' => false, 'message' => 'Only admins can verify reviews'], 403);
        }

        $review->is_verified = true;
        $review->save();

        return response()->json([
            'status' => true,
            'message' => 'Review verified',
            'data' => $review->fresh()->load(['patient.user', 'doctor.user'])
        ]);
    }

    /**
     * Recalculate the doctor's average rating.
     */
    protected function recalculateDoctorRating(Doctor $doctor)
    {
        $avg = $doctor->reviews()->avg('rating');
        $doctor->rating = $avg ? round($avg, 2) : 0;
        $doctor->save();
    }

}
