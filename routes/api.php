<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\HabitacionTipoController;
use App\Http\Controllers\Api\AcomodacionController;
use App\Http\Controllers\Api\HabitacionController;

// Ruta de prueba por defecto
Route::get('/ping', function () {
    return response()->json(['message' => 'API Hoteles Decameron'], 200);
});

// Rutas tipo REST
Route::apiResource('hoteles',HotelController::class);
Route::apiResource('habitacionTipos',HabitacionTipoController::class);
Route::apiResource('acomodaciones',AcomodacionController::class);
//Route::apiResource('habitaciones',HabitacionController::class);
Route::get('habitaciones', [HabitacionController::class, 'index']);
Route::post('habitaciones', [HabitacionController::class, 'store']);
Route::put('habitaciones/{id}', [HabitacionController::class, 'update']);
Route::delete('habitaciones/{id}', [HabitacionController::class, 'destroy']);

Route::get('/hoteles/{id}/habitaciones/total', [HabitacionController::class, 'totalPorHotel']);
Route::get('/hoteles/habitaciones/detalle', [HotelController::class, 'hotelesConDetalleHabitaciones']);
Route::get('/hoteles/{id}/detalle-habitaciones', [HotelController::class, 'hotelConDetalleHabitaciones']);
// Route en Laravel
Route::get('/habitaciones/combinaciones-validas', [HabitacionController::class, 'getCombinacionesValidas']);



