<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Utente extends Model
{

    protected $table = 'utenti';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    use HasFactory;

}
