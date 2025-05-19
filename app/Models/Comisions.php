<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comisions extends Model
{
    protected $table = 'commissions';
    protected $fillable = [
        'affiliate_id',
        'amount',
        'description',
        'generated_at',
    ];


    public function afiliat()
    {
        return $this->belongsTo(User::class, 'afiliat_id');
    }
    public function getAmountAttribute($value)
    {
        return number_format((float) $value, 2, '.', '');
    }

}
