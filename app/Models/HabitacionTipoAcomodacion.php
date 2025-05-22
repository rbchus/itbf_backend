<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HabitacionTipoAcomodacion extends Model
{
    use HasFactory;
    protected $table = 'habitacion_acomodacion';
    public $timestamps = false;
    protected $primaryKey = ['h_id', 'a_id'];
    public $incrementing = false;
    protected $fillable = ['h_id', 'a_id'];
}
