<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $table = 'affiliate_links';
    protected $fillable = [
        'affiliate_id',
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

    public function affiliate() {
        return $this->belongsTo(User::class, 'affiliate_id');
    }
}
