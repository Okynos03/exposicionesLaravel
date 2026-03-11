<?php

use Illuminate\Support\Facades\Route;
// Importación de todos tus controladores
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\MaestroController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\ExposicionController;
use App\Http\Controllers\RubricaController;
use App\Http\Controllers\EvaluacionController;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/
// Cualquiera puede intentar loguearse
Route::post('/login', [AuthController::class, 'login'])->name('login');


/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS (Requieren Token de Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // --- Autenticación y Usuario Actual ---
    Route::get('/me', [AuthController::class, 'me']); // El endpoint que faltaba
    Route::post('/logout', [AuthController::class, 'logout']);

    // --- CRUDs Automáticos (apiResource) ---
    // Esto crea GET, POST, PUT y DELETE para cada uno
    Route::middleware('SoloMaestro')->group(function(){
        Route::apiResource('alumnos', AlumnoController::class);
        Route::apiResource('maestros', MaestroController::class);
        Route::apiResource('rubricas', RubricaController::class);
        Route::apiResource('grupos', GrupoController::class);
    });
   
    Route::apiResource('exposiciones', ExposicionController::class);
    Route::apiResource('evaluaciones', EvaluacionController::class);
    Route::apiResource('equipos', EquipoController::class);

    // --- Rutas Especiales (Lógica Extra) ---

    // Inscribir lista de alumnos a un grupo
    Route::post('grupos/{id}/inscribir', [GrupoController::class, 'inscribirAlumnos']);

    // Actualizar integrantes de un equipo
    Route::put('equipos/{id}/integrantes', [EquipoController::class, 'updateIntegrantes']);

});