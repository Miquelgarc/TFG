<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $table = 'rental_properties';
    protected $fillable = [
        'title', 'description', 'price_per_night', 'location', 'image_url'
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
