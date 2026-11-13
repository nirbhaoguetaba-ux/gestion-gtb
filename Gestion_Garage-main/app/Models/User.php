<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'Nom',
        'Prenom',
        'username',
        'email',
        'NumeroTelephone',
        'password',
        'role',
        'Adresse',
        'avatar',
        'specialite',
        'statut',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function client()
    {
        return $this->hasOne(Client::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function mecanique()
    {
        return $this->hasOne(Mecanique::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isTechnicien(): bool
    {
        return $this->role === 'technicien' || $this->role === 'mecanicien';
    }

    public function isMechanic(): bool
    {
        return $this->isTechnicien();
    }

    public function getFullNameAttribute(): string
    {
        return trim(($this->Prenom ?? '') . ' ' . ($this->Nom ?? ''));
    }

}
