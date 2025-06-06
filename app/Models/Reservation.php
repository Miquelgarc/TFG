<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'property_id',
        'user_id',
        'affiliate_link_id',
        'check_in_date',
        'check_out_date',
        'total_price',
        'status',
        'created_at'
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function affiliateLink()
    {
        return $this->belongsTo(AffiliateLink::class);
    }

    public function affiliateUser()
    {
        return $this->hasOneThrough(
            User::class,
            AffiliateLink::class,
            'id',
            'id',
            'affiliate_link_id',
            'user_id'
        );
    }
    public function commissions()
    {
        return $this->hasMany(Commission::class, 'reservation_id');
    }
}
