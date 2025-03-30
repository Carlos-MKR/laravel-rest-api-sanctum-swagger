<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmployeeController;

// Rutas de autenticación
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    // Obtener todos los empleados
    Route::get('/employees', [EmployeeController::class, 'index']);
    // Obtener un empleado
    Route::get('/employees/{id}', [EmployeeController::class, 'show']);
    // Crear un empleado
    Route::post('/employees', [EmployeeController::class, 'store']);
    // Actualizar un empleado
    Route::put('/employees/{id}', [EmployeeController::class, 'update']);
    // Actualizar un empleado parcialmente
    Route::patch('/employees/{id}', [EmployeeController::class, 'updatePartial']);
    // Eliminar un empleado
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy']);
});