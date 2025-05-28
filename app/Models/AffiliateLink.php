<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateLink extends Model
{
    protected $fillable = [
        'affiliate_id',
        'target_url',
        'generated_url',
        'clicks',
        'conversions',
    ];


    public function affiliateUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

}
