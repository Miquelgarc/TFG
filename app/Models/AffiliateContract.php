<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateContract extends Model
{
    protected $fillable = [
        'affiliate_id',
        'affiliate_level_id',
        'starts_at',
        'ends_at',
    ];

    public function affiliate()
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }

    public function level()
    {
        return $this->belongsTo(AffiliateLevel::class, 'affiliate_level_id');
    }
}
