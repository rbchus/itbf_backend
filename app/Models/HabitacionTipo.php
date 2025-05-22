<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HabitacionTipo extends Model
{
    use HasFactory;
     protected $table = 'habitacion_tipo';
     protected $primaryKey = 'id';
     public $timestamps = false;

    protected $fillable = [
        'descripcion',
    ];
}
