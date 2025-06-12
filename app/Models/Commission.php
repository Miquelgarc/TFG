<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $fillable = [
        'affiliate_id',
        'reservation_id',
        'amount',
        'description',
        'generated_at',
        'status',
        'invoice_id',
        'is_paid',
        'paid_at',
    ];

    public function affiliate()
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function getAmountAttribute($value)
    {
        return number_format((float) $value, 2, '.', '');
    }
}
