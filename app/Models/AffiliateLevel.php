<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateLevel extends Model
{
    protected $fillable = [
        'name',
        'commission_percentage',
        'min_reservations',
        'min_clicks',
        'min_total_earnings',
    ];

    public function contracts()
    {
        return $this->hasMany(AffiliateContract::class, 'affiliate_level_id');
    }
}
