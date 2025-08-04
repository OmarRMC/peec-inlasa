<?php

use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\CategoriaLaboratorioController;
use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\DepartamentoController;
use App\Http\Controllers\Admin\EnsayoAptitudController;
use App\Http\Controllers\Admin\FormularioController;
use App\Http\Controllers\Admin\InscripcionPaqueteController;
use App\Http\Controllers\Admin\LaboratorioController;
use App\Http\Controllers\Admin\MunicipioController;
use App\Http\Controllers\Admin\NivelLaboratorioController;
use App\Http\Controllers\Admin\PagoController;
use App\Http\Controllers\Admin\PaisController;
use App\Http\Controllers\Admin\PaqueteController;
use App\Http\Controllers\Admin\ProgramaController;
use App\Http\Controllers\Admin\ProvinciaController;
use App\Http\Controllers\Admin\TipoLaboratorioController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\VerificacionCorreoLaboratorioController;
use App\Http\Controllers\Lab\LabController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\PdfInscripcionController;
use App\Http\Controllers\responsable\LaboratorioController as ResponsableLaboratorioController;
use App\Models\Permiso;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'usuario.activo'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::middleware(['auth', 'usuario.activo'])->prefix('admin')->group(function () {
    Route::get('/cargos/data', [CargoController::class, 'getData'])->name('cargos.data');
    Route::resource('cargos', CargoController::class)->except(['show']);
    Route::resource('permiso', PermisoController::class)->except(['show']);
    Route::get('/usuario/ajax/data', [UserController::class, 'getData'])->name('usuario.data');
    Route::resource('/usuario', UserController::class);
    Route::resource('/programa', ProgramaController::class)->except(['show']);
    Route::resource('/area', AreaController::class)->except(['show']);
    Route::resource('/paquete', PaqueteController::class)->except(['show']);
    Route::resource('/ensayo_aptitud', EnsayoAptitudController::class)->except(['show']);

    Route::resource('/pais', PaisController::class)->parameters(['pais' => 'pais'])->except(['show']);
    Route::resource('/departamento', DepartamentoController::class)->except(['show']);
    Route::resource('/provincia', ProvinciaController::class)->parameters(['provincia' => 'provincia'])->except(['show']);
    Route::resource('/municipio', MunicipioController::class)->parameters(['municipio' => 'municipio'])->except(['show']);

    Route::resource('nivel_laboratorio', NivelLaboratorioController::class);
    Route::resource('tipo_laboratorio', TipoLaboratorioController::class);
    Route::resource('categoria_laboratorio', CategoriaLaboratorioController::class);
    Route::resource('laboratorio', LaboratorioController::class);
    Route::get('laboratorio/ajax/data', [LaboratorioController::class, 'getData'])->name('laboratorio.ajax.data');
    Route::resource('laboratorio', LaboratorioController::class);

    Route::prefix('inscripcion')->group(function () {
        Route::get('/', [InscripcionPaqueteController::class, 'index'])->name('inscripcion_paquete.index');
        Route::get('/{lab}', [InscripcionPaqueteController::class, 'show'])
            ->name('inscripcion_paquete.show');
        Route::get('ajax/data', [InscripcionPaqueteController::class, 'getInscripcionesData'])->name('inscripcion_paquete.ajax.data');
        Route::get('/create/{lab}', [InscripcionPaqueteController::class, 'create'])
            ->name('inscripcion.create');
        Route::post('/paquetes', [InscripcionPaqueteController::class, 'store'])->name('inscripcion-paquetes.store');
        Route::post('/{id}/aprobar', [InscripcionPaqueteController::class, 'aprobarInscripcion'])->name('inscripcion-paquetes.aprobar');
        Route::post('/{id}/anular', [InscripcionPaqueteController::class, 'anularInscripcion'])->name('inscripcion-paquetes.anular');
        Route::post('/{id}/revision', [InscripcionPaqueteController::class, 'enRevision'])->name('inscripcion-paquetes.enRevision');
        Route::post('/{id}/obs', [InscripcionPaqueteController::class, 'obsInscripcion'])->name('inscripcion-paquetes.obserbaciones');
    });

    Route::post('/pago', [PagoController::class, 'store'])->name('pago.store');
    Route::delete('/pago/{pago}', [PagoController::class, 'destroy'])->name('pago.destroy');
    Route::get('/paquetes/programa', [PaqueteController::class, 'getPaquetesPorPrograma'])->name('paquetes.programa');

    Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
    Route::put('/configuracion/update/{seccion}', [ConfiguracionController::class, 'update'])->name('configuracion.update');

    Route::resource('formularios', FormularioController::class);
});
Route::middleware(['auth', 'usuario.activo', 'canany:' . Permiso::ADMIN . ',' . Permiso::GESTION_INSCRIPCIONES])->prefix('reporte')->group(function () {
    Route::get('/inscripcion-lab-paquetes-pdf/{id}', [PdfInscripcionController::class, 'generar'])->name('formulario_inscripcion_lab.pdf');
    Route::get('/contrato-lab-paquetes-pdf/{id}', [PdfInscripcionController::class, 'generarContrato'])->name('formulario_contrato_lab.pdf');
});

Route::middleware(['auth', 'usuario.activo'])->prefix('lab')->group(function () {
    Route::get('/profile', [LabController::class, 'profile'])->name('lab.profile');
    Route::get('/editar-profile', [LabController::class, 'editarProfile'])->name('lab.profile.edit');
    Route::get('/', [LabController::class, 'index'])->name('lab.ins.index');
    Route::get('/inscripciones/ajax/data', [LabController::class, 'getInscripcionData'])->name('lab_inscripcion.ajax.data');
    Route::get('/inscripciones/new', [LabController::class, 'labInscripcion'])->name('lab.inscripcion.create');
    Route::get('/inscripcion/{id}', [LabController::class, 'labShowInscripcion'])->name('lab.inscripcion.show');
    Route::get('/notificacion/verify', [NotificacionController::class, 'getNotificacion']);
    Route::post('/notificacion/read', [NotificacionController::class, 'marcarLeido']);
    Route::get('/contrato', [LabController::class, 'generarContrato'])->name('formulario_contrato');
    Route::get('/formulario-ins/{id}', [LabController::class, 'generarFormularioIns'])->name('formulario_inscripcion');
});

Route::middleware(['auth', 'usuario.activo'])->prefix('responsable')->group(function () {
    Route::get('/ea/{id}/labs', [ResponsableLaboratorioController::class, 'index'])->name('ea.lab.inscritos');
    Route::get('/ea/{id}/labs-ajax', [ResponsableLaboratorioController::class, 'getData'])->name('ea.lab.inscritos.ajax');
});

require __DIR__ . '/auth.php';
