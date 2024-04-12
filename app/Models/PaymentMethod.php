<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_type', 
        'card_number', 
        'expiry', 
        'cvv', 
        'card_name', 
        'status'
    ];

    protected $encryptable = [
        'card_number', 
        'expiry', 
        'cvv', 
        'card_name'
    ];

    public function setCardNumberAttribute($value)
    {
        $this->attributes['card_number'] = encrypt($value);
    }
    
    public function getCardNumberAttribute($value)
    {
        return decrypt($value);
    }

    public function setExpiryAttribute($value)
    {
        $this->attributes['expiry'] = encrypt($value);
    }

    public function getExpiryAttribute($value)
    {
        return decrypt($value);
    }

    public function setCvvAttribute($value)
    {
        $this->attributes['cvv'] = encrypt($value);
    }

    public function getCvvAttribute($value)
    {
        return decrypt($value);
    }

    public function setCardNameAttribute($value)
    {
        $this->attributes['card_name'] = encrypt($value);
    }

    public function getCardNameAttribute($value)
    {
        return decrypt($value);
    }
}

