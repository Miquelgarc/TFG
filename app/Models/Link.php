<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $table = 'affiliate_links';
    protected $fillable = [
        'name',
        'affiliate_id',
        'property_id',
        'target_url',
        'generated_url',
        'clicks',
        'conversions',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getLinkAttribute($value)
    {
        return url($value);
    }

    public function affiliate()
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'affiliate_link_id');
    }
    
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function clicks()
    {
        return $this->hasMany(AffiliateClick::class);
    }

}
