<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    // Usa la tabella "users" (clienti)
    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = true; // hai created_at e updated_at

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
        'created_at',
        'updated_at',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ------- Scopes ------- */
    public function scopeSearch($q, ?string $s)
    {
        if (!$s) return $q;
        return $q->where('email','like',"%{$s}%")
            ->orWhere('ragione_sociale','like',"%{$s}%")
            ->orWhere('telefono','like',"%{$s}%");
    }

    public function scopeDateFrom($q, ?string $from)
    {
        if ($from) $q->whereDate('created_at','>=',$from);
        return $q;
    }

    public function scopeDateTo($q, ?string $to)
    {
        if ($to) $q->whereDate('created_at','<=',$to);
        return $q;
    }
}
