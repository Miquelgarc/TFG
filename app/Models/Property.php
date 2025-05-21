<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $table = 'rental_properties';
    protected $fillable = [
        'title', 'description', 'price_per_night', 'location', 'max_guests', 'image_url', 'is_available'
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    
}
