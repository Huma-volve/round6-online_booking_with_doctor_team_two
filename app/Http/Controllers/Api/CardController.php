<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Card;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreCardRequest;
use App\Http\Requests\UpdateCardRequest;
use App\Traits\ApiResponseTrait;

class CardController extends Controller
{
     use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;
     use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */

    //GET
    public function index()
    {
        //
        $cards = Auth::user()->patient->cards;
        return $this->successResponse($cards);
    }

    /**
     * Store a newly created resource in storage.
     */

    //POST
   public function store(StoreCardRequest $request)
{
   
    $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
    $user = Auth::user();

    // Create Stripe customer if not exists
    if (!$user->stripe_customer_id) {
        $customer = $stripe->customers->create([
            'email' => $user->email,
            'name'  => $user->full_name,
        ]);
        /** @var \App\Models\User $user */
$user = Auth::user();

            $user->update(['stripe_customer_id' => $customer->id]);
    }

    // Attach payment method to customer
    $stripe->paymentMethods->attach(
        $request->payment_method_id,
        ['customer' => $user->stripe_customer_id]
    );

    // Retrieve payment method details from Stripe
    $paymentMethod = $stripe->paymentMethods->retrieve($request->payment_method_id);

    // Save to DB
    $card = Card::create([
        'patient_id'       => $user->patient->id,
        'CardHolderName'   => $paymentMethod->billing_details->name,
        'last4'            => $paymentMethod->card->last4,
        'Type'             => $paymentMethod->card->brand,
        'token'            => $paymentMethod->id, // Stripe PM ID
    ]);

    return $this->successResponse($card, 'Card added successfully', 201);
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
    public function update(UpdateCardRequest $request,  Card $card)
    {
        //
         $this->authorize('update', $card);
        $card->update($request->only(['CardHolderName', 'Type', 'token']));

        return $this->successResponse($card, 'Card updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
     public function destroy(Card $card)
    {
        $this->authorize('delete', $card);

        $card->delete();

        return $this->successResponse(null, 'Card deleted');
    }
}
