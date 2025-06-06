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


    public function affiliate()
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'affiliate_link_id');
    }

    public function clicks()
    {
        return $this->hasMany(AffiliateClick::class);
    }


}
