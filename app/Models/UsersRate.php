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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
