<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Allineato alla tua tabella users
    protected $fillable = [
        'ragione_sociale',
        'piva',
        'codice_fiscale',
        'email',
        'password',
        'telefono',
        'indirizzo',
        'cap',
        'citta',
        'provincia',
        'note',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        // Se assegni una password in chiaro, Laravel la hasha da solo
        'password' => 'hashed',
    ];

    public function rates()
    {
        return $this->hasMany(UsersRate::class, 'user_id');
    }
}
