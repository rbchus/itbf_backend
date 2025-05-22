<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\Habitacion;
use Illuminate\Support\Facades\Validator;

class HotelController extends Controller
{
  public function index()
{
    $hoteles = Hotel::all();

    if ($hoteles->isEmpty()) {
        return response()->json([
            'status' => 'success',
            'codigo' => 200,
            'error' => null,
            'mensaje' => 'No hay hoteles registrados en el sistema',
            'datos' => []
        ], 200);
    }

    return response()->json([
        'status' => 'success',
        'codigo' => 200,
        'error' => null,
        'mensaje' => 'Listado de hoteles obtenido correctamente',
        'datos' => $hoteles
    ], 200);
}


   
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'nombre' => 'required|unique:hoteles,nombre',
        'direccion' => 'required',
        'ciudad' => 'required',
        'nit' => 'required|unique:hoteles,nit',
        'h_total' => 'required|integer|min:1'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'codigo' => 422,
            'error' => 'Validación fallida',
            'mensaje' => 'Los datos enviados no son válidos o ya existen',
            'datos' => $validator->errors()
        ], 422);
    }

    try {
        $hotel = Hotel::create($request->all());

        return response()->json([
            'status' => 'success',
            'codigo' => 201,
            'error' => null,
            'mensaje' => 'Hotel creado correctamente',
            'datos' => $hotel
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'codigo' => 500,
            'error' => 'Excepción al crear hotel',
            'mensaje' => 'Ocurrió un error inesperado al guardar el hotel',
            'datos' => $e->getMessage()
        ], 500);
    }
}



 public function show($id)
{
    $hotel = Hotel::find($id);

    if (!$hotel) {
        return response()->json([
            'status' => 'error',
            'codigo' => 404,
            'error' => 'Hotel no encontrado',
            'mensaje' => 'No se encontró un hotel con el ID proporcionado',
            'datos' => null
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'codigo' => 200,
        'error' => null,
        'mensaje' => 'Hotel encontrado correctamente',
        'datos' => $hotel
    ], 200);
}


  public function update(Request $request, $id)
{
    $hotel = Hotel::find($id);

    if (!$hotel) {
        return response()->json([
            'status' => 'error',
            'codigo' => 404,
            'error' => 'Hotel no encontrado',
            'mensaje' => 'No se encontró un hotel con el ID proporcionado',
            'datos' => null
        ], 404);
    }

    $validator = Validator::make($request->all(), [
        'nombre' => 'sometimes|required|unique:hoteles,nombre,' . $id,
        'direccion' => 'sometimes|required',
        'ciudad' => 'sometimes|required',
        'nit' => 'sometimes|required|unique:hoteles,nit,' . $id,
        'h_total' => 'sometimes|required|integer|min:1'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'codigo' => 422,
            'error' => 'Validación fallida',
            'mensaje' => 'Los datos enviados no son válidos o ya existen',
            'datos' => $validator->errors()
        ], 422);
    }
// Asegúrate que el hotel esté correctamente cargado


    // Validación personalizada: h_total >= habitaciones existentes
    if ($request->has('h_total')) {
           $habitacionesAsignadas = $hotel->habitaciones->sum('cantidad');; // Asegúrate que la relación 'habitaciones' esté definida en el modelo Hotel

        if ($request->input('h_total') < $habitacionesAsignadas) {
            return response()->json([
                'status' => 'error',
                'codigo' => 422,
                'error' => 'h_total inválido',
                'mensaje' => 'El total de habitaciones no puede ser menor que las habitaciones asignadas al hotel (' . $habitacionesAsignadas . ')',
                'datos' => ['h_total' => 'Valor mínimo permitido: ' . $habitacionesAsignadas]
            ], 422);
        }
    }

    try {
        $hotel->update($request->all());

        return response()->json([
            'status' => 'success',
            'codigo' => 200,
            'error' => null,
            'mensaje' => 'Hotel actualizado correctamente',
            'datos' => $hotel
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'codigo' => 500,
            'error' => 'Error al actualizar',
            'mensaje' => 'Ocurrió un error inesperado al actualizar el hotel',
            'datos' => $e->getMessage()
        ], 500);
    }
}



    public function destroy($id)
{
    $hotel = Hotel::find($id);

    if (!$hotel) {
        return response()->json([
            'status' => 'error',
            'codigo' => 404,
            'error' => 'Hotel no encontrado',
            'mensaje' => 'No se encontró un hotel con el ID proporcionado',
            'datos' => null
        ], 404);
    }

    try {
        $hotel->delete();

        return response()->json([
            'status' => 'success',
            'codigo' => 200,
            'error' => null,
            'mensaje' => 'Hotel eliminado satisfactoriamente',
            'datos' => null
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'codigo' => 500,
            'error' => 'Error al eliminar',
            'mensaje' => 'Ocurrió un error inesperado al eliminar el hotel',
            'datos' => $e->getMessage()
        ], 500);
    }
}

public function hotelesConDetalleHabitaciones()
{
    $hoteles = Hotel::with(['habitaciones.habitacionTipo', 'habitaciones.acomodaciones'])
        ->get()
        ->map(function ($hotel) {
            $totalAsignadas = $hotel->habitaciones->sum('cantidad');

            return [
                'id' => $hotel->id,
                'nombre' => $hotel->nombre,
                'ciudad' => $hotel->ciudad,
                'direccion' => $hotel->direccion,
                'nit' => $hotel->nit,
                'h_total' => $hotel->h_total,
                'habitaciones_asignadas' => $totalAsignadas,
                'habitaciones_disponibles' => $hotel->h_total - $totalAsignadas,
                'detalle_habitaciones' => $hotel->habitaciones->map(function ($habitacion) {
                    return [
                        'id' => $habitacion->id,
                        'tipo_habitacion' => $habitacion->habitacionTipo->descripcion ?? null,
                        'acomodacion' => $habitacion->acomodaciones->descripcion ?? null,
                        'cantidad' => $habitacion->cantidad,
                    ];
                }),
            ];
        });

    return response()->json([
        'status' => 'success',
        'codigo' => 200,
        'error' => null,
        'mensaje' => 'Listado de hoteles con detalle de habitaciones',
        'datos' => $hoteles
    ], 200);
}

public function hotelConDetalleHabitaciones($id)
{
    $hotel = Hotel::with(['habitaciones.habitacionTipo', 'habitaciones.acomodaciones'])->find($id);

    if (!$hotel) {
        return response()->json([
            'status' => 'error',
            'codigo' => 404,
            'error' => ['hotel' => ['Hotel no encontrado']],
            'mensaje' => 'No se encontró el hotel con el ID proporcionado',
            'datos' => null
        ], 404);
    }

    $totalAsignadas = $hotel->habitaciones->sum('cantidad');

    return response()->json([
        'status' => 'success',
        'codigo' => 200,
        'error' => null,
        'mensaje' => 'Detalle del hotel con habitaciones asignadas',
        'datos' => [
            'id' => $hotel->id,
            'nombre' => $hotel->nombre,
            'ciudad' => $hotel->ciudad,
            'direccion' => $hotel->direccion,
            'nit' => $hotel->nit,
            'h_total' => $hotel->h_total,
            'habitaciones_asignadas' => $totalAsignadas,
            'habitaciones_disponibles' => $hotel->h_total - $totalAsignadas,
            'detalle_habitaciones' => $hotel->habitaciones->map(function ($habitacion) {
                return [
                    'tipo_habitacion' => $habitacion->habitacionTipo->descripcion ?? null,
                    'acomodacion' => $habitacion->acomodaciones->descripcion ?? null,
                    'cantidad' => $habitacion->cantidad,
                ];
            }),
        ]
    ], 200);
}



}
