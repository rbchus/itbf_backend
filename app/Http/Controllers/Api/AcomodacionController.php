<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Acomodacion;

class AcomodacionController extends Controller
{
     public function index()
{
    $acomodaciones = Acomodacion::all();

    if ($acomodaciones->isEmpty()) {
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
        'mensaje' => 'Listado de acomadaciones obtenido correctamente',
        'datos' => $acomodaciones
    ], 200);
}
}
