<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mail extends Model
{

    protected $table = 'mailinglist_mail';
    protected $primaryKey = 'id';

    protected $fillable = [
        'mailinglistID',
        'email',
        'nome',
        'cognome',
    ];

    public $incrementing = true;
    public $timestamps = false;

    use HasFactory;

}
