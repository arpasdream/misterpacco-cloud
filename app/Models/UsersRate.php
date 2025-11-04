<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersRate extends Model
{
    protected $table = 'users_rates';

    protected $fillable = [
        'user_id',
        'min_peso',
        'max_peso',
        'prezzo',
    ];

    public $timestamps = true;

    protected $casts = [
        'min_peso' => 'decimal:2',
        'max_peso' => 'decimal:2',
        'prezzo'   => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
