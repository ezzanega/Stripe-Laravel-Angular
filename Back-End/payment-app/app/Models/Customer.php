<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'stripe_customer_id',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function paymentIntents()
    {
        return $this->hasMany(PaymentIntent::class);
    }
}
