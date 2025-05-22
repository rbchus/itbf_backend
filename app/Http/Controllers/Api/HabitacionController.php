<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Habitacion;
use App\Models\HabitacionTipoAcomodacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HabitacionController extends Controller
{


    
    public function index()
    {
    $habitaciones = Habitacion::with(['hotel', 'habitacionTipo', 'acomodaciones'])->get();

    return response()->json([
        'status' => 'success',
        'codigo' => 200,
        'error' => null,
        'mensaje' => 'Listado de habitaciones obtenido correctamente',
        'datos' => $habitaciones
    ], 200);
    }


public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'hotel_id' => 'required|exists:hoteles,id',
        'h_id' => 'required|exists:habitacion_tipo,id',
        'a_id' => 'required|exists:acomodacion,id',
        'cantidad' => 'required|integer|min:1'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'codigo' => 422,
            'error' => $validator->errors(),
            'mensaje' => 'Error de validación',
            'datos' => null
        ], 422);
    }

    // Validar que la combinación h_id + a_id exista en habitacion_acomodacion
    $combinacionValida = HabitacionTipoAcomodacion::where('h_id', $request->h_id)
        ->where('a_id', $request->a_id)
        ->exists();

    if (!$combinacionValida) {
        return response()->json([
            'status' => 'error',
            'codigo' => 400,
            'error' => ['combinacion' => ['La combinación de tipo de habitación y acomodación no es válida.']],
            'mensaje' => 'Combinación no permitida',
            'datos' => null
        ], 400);
    }

    // Validar que no exista ya la misma llave primaria compuesta (hotel_id, h_id, a_id)
    $existe = Habitacion::where('hotel_id', $request->hotel_id)
        ->where('h_id', $request->h_id)
        ->where('a_id', $request->a_id)
        ->exists();

    if ($existe) {
        return response()->json([
            'status' => 'error',
            'codigo' => 409,
            'error' => ['habitacion' => ['Ya existe una habitación con esta combinación de hotel, tipo y acomodación.']],
            'mensaje' => 'Registro duplicado',
            'datos' => null
        ], 409);
    }

    // Validar que la suma de habitaciones no supere el total registrado en hoteles.h_total
    $hotel = Hotel::find($request->hotel_id);
    $totalHabitacionesActuales = Habitacion::where('hotel_id', $request->hotel_id)->sum('cantidad');
    $totalNuevo = $totalHabitacionesActuales + $request->cantidad;

    if ($totalNuevo > $hotel->h_total) {
        return response()->json([
            'status' => 'error',
            'codigo' => 422,
            'error' => ['cantidad' => ['La cantidad total de habitaciones supera el total permitido para este hotel.']],
            'mensaje' => 'Cantidad excedida',
            'datos' => null
        ], 422);
    }

    // Crear habitación
    $habitacion = Habitacion::create($request->all());

    return response()->json([
        'status' => 'success',
        'codigo' => 201,
        'error' => null,
        'mensaje' => 'Habitación creada correctamente',
        'datos' => $habitacion
    ], 201);
}



public function update(Request $request, $id)
{
    $habitacion = Habitacion::find($id);
    if (!$habitacion) {
        return response()->json([
            'status' => 'error',
            'codigo' => 404,
            'error' => ['id' => ['Habitación no encontrada']],
            'mensaje' => 'No se encontró la habitación',
            'datos' => null
        ], 404);
    }

    $validator = Validator::make($request->all(), [
        'hotel_id' => 'required|exists:hoteles,id',
        'h_id' => 'required|exists:habitacion_tipo,id',
        'a_id' => 'required|exists:acomodacion,id',
        'cantidad' => 'required|integer|min:1'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'codigo' => 422,
            'error' => $validator->errors(),
            'mensaje' => 'Error de validación',
            'datos' => null
        ], 422);
    }

    // Validación de combinación h_id y a_id
    $combinacionValida = HabitacionTipoAcomodacion::where('h_id', $request->h_id)
        ->where('a_id', $request->a_id)
        ->exists();

    if (!$combinacionValida) {
        return response()->json([
            'status' => 'error',
            'codigo' => 400,
            'error' => ['combinacion' => ['La combinación de tipo de habitación y acomodación no es válida.']],
            'mensaje' => 'Combinación no permitida',
            'datos' => null
        ], 400);
    }

    // Validar que la suma de habitaciones no supere el total registrado en hoteles.h_total
    $hotel = Hotel::find($request->hotel_id);
    $totalHabitacionesActuales = Habitacion::where('hotel_id', $request->hotel_id)->sum('cantidad');
    $totalNuevo = $totalHabitacionesActuales + $request->cantidad;

    if ($totalNuevo > $hotel->h_total) {
        return response()->json([
            'status' => 'error',
            'codigo' => 422,
            'error' => ['cantidad' => ['La cantidad total de habitaciones supera el total permitido para este hotel.']],
            'mensaje' => 'Cantidad excedida',
            'datos' => null
        ], 422);
    }

    $habitacion->update($request->all());

    return response()->json([
        'status' => 'success',
        'codigo' => 200,
        'error' => null,
        'mensaje' => 'Habitación actualizada correctamente',
        'datos' => $habitacion
    ]);
}


public function destroy($id)
{
    $habitacion = Habitacion::find($id);
    if (!$habitacion) {
        return response()->json([
            'status' => 'error',
            'codigo' => 404,
            'error' => 'NoEncontrado',
            'mensaje' => 'Habitación no encontrada',
            'datos' => null
        ], 404);
    }

    $habitacion->delete();

    return response()->json([
        'status' => 'success',
        'codigo' => 200,
        'error' => null,
        'mensaje' => 'Habitación eliminada satisfactoriamente',
        'datos' => null
    ], 200);
}


public function totalPorHotel($id)
{
    $hotel = Hotel::find($id);

    if (!$hotel) {
        return response()->json([
            'status' => 'error',
            'codigo' => 404,
            'error' => ['hotel' => ['Hotel no encontrado']],
            'mensaje' => 'No se encontró el hotel solicitado',
            'datos' => null
        ], 404);
    }

    $totalHabitaciones = Habitacion::where('hotel_id', $id)->sum('cantidad');

    return response()->json([
        'status' => 'success',
        'codigo' => 200,
        'error' => null,
        'mensaje' => 'Total de habitaciones asignadas al hotel',
        'datos' => [
            'hotel_id' => $id,
            'nombre' => $hotel->nombre,
            'total_habitaciones_registradas' => $totalHabitaciones,
            'h_total' => $hotel->h_total
        ]
    ], 200);
}
// Método en HabitacionController
public function getCombinacionesValidas()
{
    $combinaciones = DB::table('habitacion_acomodacion')
        ->join('habitacion_tipo', 'habitacion_tipo.id', '=', 'habitacion_acomodacion.h_id')
        ->join('acomodacion', 'acomodacion.id', '=', 'habitacion_acomodacion.a_id')
        ->select(
            'habitacion_acomodacion.h_id',
            'habitacion_acomodacion.a_id',
            'habitacion_tipo.descripcion as tipo',
            'acomodacion.descripcion as acomodacion'
        )
        ->get();

    return response()->json($combinaciones);
}

}
