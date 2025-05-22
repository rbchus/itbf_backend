<?php


namespace App\Models;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Habitacion;
use App\Models\HabitacionTipo;
use App\Models\Acomodacion;

class Hotel extends Model
{
    use HasFactory;
     protected $table = 'hoteles';
     protected $primaryKey = 'id';
     public $timestamps = false;

    protected $fillable = [
        'nombre',
        'direccion',
        'ciudad',
        'nit',
        'h_total',
    ];


      public function habitacionesall()
    {
        return $this->hasMany(Habitacion::class);
       
    }


    /**
     * Relación uno a muchos con habitaciones de hotel.
     */
    public function habitaciones()
    {
        //return $this->hasMany(Habitacion::class);
        return $this->hasMany(Habitacion::class, 'hotel_id');
    }

    /**
     * Relación muchos a muchos con tipos de habitación a través de hotel_rooms.
     */
    public function habitacionTipos()
    {
        return $this->belongsToMany(HabitacionTipo::class, 'habitaciones')
            ->withPivot('h_id', 'cantidad')
            ->withTimestamps();
    }

    /**
     * Relación muchos a muchos con tipos de acomodación a través de hotel_rooms.
     */
    public function acomodaciones()
    {
        return $this->belongsToMany(Acomodacion::class, 'habitaciones')
            ->withPivot('a_id', 'cantidad')
            ->withTimestamps();
    }
}
