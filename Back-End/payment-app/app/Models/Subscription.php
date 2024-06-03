<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'customer_id', 'stripe_subscription_id', 'stripe_price_id', 'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
