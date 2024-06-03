<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentIntent extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id', 'stripe_payment_intent_id', 'amount', 'currency', 'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
