<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HabitacionTipo;

class HabitacionTipoController extends Controller
{
    public function index()
{
    $habitacionestipo = HabitacionTipo::all();

    if ($habitacionestipo->isEmpty()) {
        return response()->json([
            'status' => 'success',
            'codigo' => 200,
            'error' => null,
            'mensaje' => 'No hay datos registrados en el sistema',
            'datos' => []
        ], 200);
    }

    return response()->json([
        'status' => 'success',
        'codigo' => 200,
        'error' => null,
        'mensaje' => 'Listado de datos  obtenido correctamente',
        'datos' => $habitacionestipo
    ], 200);
}
}
