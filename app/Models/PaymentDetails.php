<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentDetails extends Model
{
    protected $table = 'payment_details';

    protected $fillable = [
        'user_id',
        'cart_id',
        'transaction_reference',
        'is_pending',
        'is_on_hold',
        'cart_amount',
        'card_scheme',
        'card_type',
        'payment_description',
    ];
}