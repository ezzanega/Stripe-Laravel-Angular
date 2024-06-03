<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\PaymentIntent as PaymentIntentModel;
use Stripe\Stripe;
use Stripe\Customer as StripeCustomer;
use Stripe\PaymentIntent;

class StripeController extends Controller{

    public function createPaymentIntent(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Retrieve or create a customer
        $customer = Customer::firstOrCreate(
            ['email' => $request->email],
            ['name' => $request->name]
        );

        // Create Stripe customer if not exists
        if (!$customer->stripe_customer_id) {
            $stripeCustomer = StripeCustomer::create([
                'email' => $customer->email,
            ]);
            $customer->stripe_customer_id = $stripeCustomer->id;
            $customer->save();
        }

        // Retrieve the plan from the database
        $plan = Plan::find($request->plan_id);

        // Create a Payment Intent
        $paymentIntent = PaymentIntent::create([
            'amount' => $plan->price * 100, // Amount in cents
            'currency' => 'mad', // Set currency to Moroccan Dirham
            'customer' => $customer->stripe_customer_id,
            'metadata' => [
                'customer_id' => $customer->id,
                'plan_id' => $plan->id,
            ],
            'automatic_payment_methods' => [
                'enabled' => true,
                'allow_redirects' => 'never',
            ],
        ]);

        // Save payment intent details in database
        PaymentIntentModel::create([
            'customer_id' => $customer->id,
            'stripe_payment_intent_id' => $paymentIntent->id,
            'amount' => $plan->price * 100,
            'currency' => 'mad', // Save currency as MAD
            'status' => $paymentIntent->status,
        ]);

        return response()->json([
            'clientSecret' => $paymentIntent->client_secret,
            'customer' => $customer
        ]);
    }




    public function handlePaymentSuccess(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);
        if ($paymentIntent->status == 'succeeded') {
            $customer = Customer::where('stripe_customer_id', $paymentIntent->customer)->first();
            $planId = $paymentIntent->metadata->plan_id;

            $subscription = Subscription::create([
                'customer_id' => $customer->id,
                'stripe_subscription_id' => $paymentIntent->id,
                'stripe_price_id' => $planId,
                'status' => 'active',
            ]);

            // Update payment intent status in database
            $paymentIntentModel = PaymentIntentModel::where('stripe_payment_intent_id', $paymentIntent->id)->first();
            $paymentIntentModel->status = $paymentIntent->status;
            $paymentIntentModel->save();

            return response()->json(['message' => 'Subscription successful']);
        }

        return response()->json(['message' => 'Payment failed'], 400);
    }

    }
