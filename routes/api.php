<?php

use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\CategoriaLaboratorioController;
use App\Http\Controllers\Admin\DepartamentoController;
use App\Http\Controllers\Admin\EnsayoAptitudController;
use App\Http\Controllers\Admin\LaboratorioController;
use App\Http\Controllers\Admin\MunicipioController;
use App\Http\Controllers\Admin\NivelLaboratorioController;
use App\Http\Controllers\Admin\PaisController;
use App\Http\Controllers\Admin\PaqueteController;
use App\Http\Controllers\Admin\ProgramaController;
use App\Http\Controllers\Admin\ProvinciaController;
use App\Http\Controllers\Admin\TipoLaboratorioController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin')->group(function () {
    Route::get('/test', function () {
        return response()->json(['message' => 'API is working']);
    });
    // Route::get('/cargos/data', [CargoController::class, 'getData'])->name('cargos.data');
    // Route::resource('cargos', CargoController::class)->except(['show']);
    // Route::resource('permiso', PermisoController::class)->except(['show']);
    // Route::get('/usuario/ajax/data', [UserController::class, 'getData'])->name('usuario.data');
    // Route::resource('/usuario', UserController::class);
    // Route::resource('/programa', ProgramaController::class)->except(['show']);
    // Route::resource('/area', AreaController::class)->except(['show']);
    // Route::resource('/paquete', PaqueteController::class)->except(['show']);
    // Route::resource('/ensayo_aptitud', EnsayoAptitudController::class)->except(['show']);

    Route::get('/departamento/{pais}', [DepartamentoController::class, 'getDataAjax']);
    Route::get('/provincia/{departamento}', [ProvinciaController::class, 'getDataAjax']);
    Route::get('/municipio/{provincia}', [MunicipioController::class, 'getDataAjax']);
    Route::get('/area/{id}/paquetes', [PaqueteController::class, 'getPaquetePorAreaAjax']);
    Route::get('/area/{id}/paquetes/ensayos', [EnsayoAptitudController::class, 'getEnsayoPorAreaAjax']);

    // Route::resource('nivel_laboratorio', NivelLaboratorioController::class);
    // Route::resource('tipo_laboratorio', TipoLaboratorioController::class);
    // Route::resource('categoria_laboratorio', CategoriaLaboratorioController::class);
    // Route::resource('laboratorio', LaboratorioController::class);
    // Route::resource('laboratorio', LaboratorioController::class);
});
