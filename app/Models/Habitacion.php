<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Hotel;
use App\Models\HabitacionTipo;
use App\Models\Acomodacion;

class Habitacion extends Model
{
    use HasFactory;
     protected $table = 'habitaciones';
     protected $primaryKey = 'id';
     public $timestamps = false;

    protected $fillable = [
        'hotel_id',
        'h_id',
        'a_id',
        'cantidad',
    ];

   
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

  
    public function habitacionTipo()
    {
        //return $this->belongsTo(HabitacionTipo::class);
        return $this->belongsTo(HabitacionTipo::class, 'h_id');
           
    }

  
    public function acomodaciones()
    {
       // return $this->belongsTo(Acomodacion::class);
       return $this->belongsTo(Acomodacion::class, 'a_id');
            
    }
}
