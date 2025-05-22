<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acomodacion extends Model
{
      use HasFactory;
     protected $table = 'acomodacion';
     protected $primaryKey = 'id';
     public $timestamps = false;

    protected $fillable = [
        'descripcion',
    ];
}
