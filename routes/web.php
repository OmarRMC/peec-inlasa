<?php

use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\EnsayoAptitudController;
use App\Http\Controllers\Admin\PaqueteController;
use App\Http\Controllers\Admin\ProgramaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\PaisController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProvinciaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/cargos/data', [CargoController::class, 'getData'])->name('cargos.data');
    Route::resource('cargos', CargoController::class)->except(['show']);
    Route::resource('permiso', PermisoController::class)->except(['show']);
    Route::get('/usuario/ajax/data', [UserController::class, 'getData'])->name('usuario.data');
    Route::resource('/usuario', UserController::class);
    Route::resource('/programa', ProgramaController::class)->except(['show']);
    Route::resource('/area', AreaController::class)->except(['show']);
    Route::resource('/paquete', PaqueteController::class)->except(['show']);
    Route::resource('/ensayo_aptitud', EnsayoAptitudController::class)->except(['show']);

    Route::resource('/pais', PaisController::class)->except(['show']);
    Route::resource('/departamento', DepartamentoController::class)->except(['show']);
    Route::resource('/provincia', ProvinciaController::class)->except(['show']);
    Route::resource('/municipio', MunicipioController::class)->except(['show']);
});
require __DIR__ . '/auth.php';
