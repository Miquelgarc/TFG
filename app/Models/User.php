<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_name',
        'website_url',
        'role_id',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    protected $with = ['role'];

    protected $appends = ['role_name'];
    public function getRoleNameAttribute()
    {
        return $this->role?->name;
    }

    public function is_admin()
    {
        return $this->role?->name === 'admin';
    }

    public function affiliateLinks()
    {
        return $this->hasMany(AffiliateLink::class, 'affiliate_id');
    }


    public function affiliateContracts()
    {
        return $this->hasMany(AffiliateContract::class, 'affiliate_id');
    }

    public function currentAffiliateContract()
    {
        return $this->hasOne(AffiliateContract::class, 'affiliate_id')->latestOfMany();
    }

    public function currentAffiliateLevel()
    {
        return $this->currentAffiliateContract?->level;
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class, 'affiliate_id');
    }

}
