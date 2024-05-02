<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id', 
        'billing_details_id', 
        'payment_method', 
        'total_price', 
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // public function billingDetails()
    // {
    //     return $this->hasOne(BillingDetail::class, 'id', 'billing_details_id');
    // }

    public function billingDetails()
    {
        return $this->belongsTo(BillingDetail::class, 'billing_details_id', 'id');
    }

}
