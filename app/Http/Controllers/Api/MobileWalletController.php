<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileWallet;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponseTrait;
use Stripe\StripeClient;

class MobileWalletController extends Controller
{
         use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;
        use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $wallets = Auth::user()->patient->mobileWallets;
        return $this->successResponse($wallets, 'Mobile wallets retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'Type' => 'required|string|in:apple_pay,google_pay,samsung_pay',
            'payment_method_id' => 'required|string',
            'last4' => 'nullable|string'
        ]);

        $stripe = new StripeClient(env('STRIPE_SECRET'));

        // attach payment method to stripe customer
        $user = Auth::user();
        if (!$user->stripe_customer_id) {
            $customer = $stripe->customers->create([
                'email' => $user->email,
                'name' => $user->full_name,
            ]);
            // $user->update(['stripe_customer_id' => $customer->id]);
        }

        $stripe->paymentMethods->attach(
            $request->payment_method_id,
            ['customer' => $user->stripe_customer_id]
        );

        $wallet = MobileWallet::create([
            'patient_id' => $user->id,
            'provider' => $request->provider,
            'stripe_payment_method_id' => $request->payment_method_id,
            'last4' => $request->last4,
        ]);

        return $this->successResponse($wallet, 'Mobile wallet added successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
     public function update(Request $request, MobileWallet $mobileWallet)
    {
        $this->authorize('update', $mobileWallet);

        $mobileWallet->update($request->only(['provider', 'last4']));
        return $this->successResponse($mobileWallet, 'Mobile wallet updated successfully');
    }

    public function destroy(MobileWallet $mobileWallet)
    {
        $this->authorize('delete', $mobileWallet);

        $mobileWallet->delete();
        return $this->successResponse(null, 'Mobile wallet deleted successfully');
    }
}

