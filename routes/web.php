<?php

use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\CategoriaLaboratorioController;
use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\DepartamentoController;
use App\Http\Controllers\Admin\DetalleCertificadoController;
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
use App\Http\Controllers\FormularioEnsayoResultadoController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\VerificacionCorreoLaboratorioController;
use App\Http\Controllers\CertificadoController;
use App\Http\Controllers\CicloController;
use App\Http\Controllers\DocumentosController;
use App\Http\Controllers\FormularioEnsayoController;
use App\Http\Controllers\GrupoSelectorController;
use App\Http\Controllers\Lab\LabController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\PdfInscripcionController;
use App\Http\Controllers\Lab\RegistroResultadosController;
use App\Http\Controllers\OpcionSelectorController;
use App\Http\Controllers\responsable\GestionFormulariosController;
use App\Http\Controllers\responsable\LaboratorioController as ResponsableLaboratorioController;
use App\Http\Controllers\VerificarController;
use App\Models\FormularioEnsayo;
use App\Models\Permiso;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'usuario.activo'])->name('dashboard');

Route::get('/verificar/{code}/certificado/{type}', [VerificarController::class, 'verificarCertificado'])->name('verificar.certificado');
Route::get('/vistas/validar_cert.php', [VerificarController::class, 'validarAntiguo']);

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
    Route::get('/searchLab', [LaboratorioController::class, 'getLabBySearch'])->name('getSearchLab');
    Route::get('/certificados', [CertificadoController::class, 'index'])->name('admin.certificado.index');
    Route::get('/certificados/ajax', [CertificadoController::class, 'getDataCertificado'])->name('admin.certificado.ajax.index');
    Route::get('/certificados/{idLaboratorio}/descargar/{gestion}/{type}', [CertificadoController::class, 'descargarCertificado'])->name('admin.certificado.descargar');

    Route::get('/formularios-ea', [FormularioEnsayoController::class, 'formulariosIndex'])->name('admin.formularios.ea');
    Route::get('/formularios-ea/{idEA}/show', [FormularioEnsayoController::class, 'formulariosByEa'])->name('admin.formularios.show');
    Route::put('/formularios-ea/update/{id}', [FormularioEnsayoController::class, 'update'])->name('admin.formularios.update');
    Route::delete('/formularios-ea/{formulario}', [FormularioEnsayoController::class, 'destroy'])->name('admin.formularios.destroy');
    Route::get('/formularios-ea/{id}/{idEa}/edit', [FormularioEnsayoController::class, 'edit'])->name('admin.formularios.edit');
    Route::get('/formularios-ea/{id}/preview', [FormularioEnsayoController::class, 'preview'])->name('admin.formularios.preview');
    Route::post('/formularios-ea', [FormularioEnsayoController::class, 'store'])->name('admin.formularios.store');
    Route::put('/formularios-ea/{id}', [FormularioEnsayoController::class, 'updateEstructura'])->name('admin.formularios.updateEstructura');
    Route::post('/formularios/usar/{id}', [FormularioEnsayoController::class, 'usar'])
        ->name('admin.formularios.usar');



    Route::get('/ciclos/{idEa}', [CicloController::class, 'index'])->name('admin.ciclos.index');
    Route::post('/ciclos', [CicloController::class, 'store'])->name('admin.ciclos.store');
    Route::put('/ciclos/{id}', [CicloController::class, 'update'])->name('admin.ciclos.update');
    Route::put('/ciclos/{id}/toggle', [CicloController::class, 'toggle'])->name('admin.ciclos.toggle');
    Route::delete('/ciclos/{id}', [CicloController::class, 'destroy'])->name('admin.ciclos.destroy');

    Route::prefix('/formularios')->name('admin.formularios.')->group(function () {
        // Route::get('/{id}/edit', [FormularioEnsayoController::class, 'edit'])->name('edit');
        Route::put('/{id}', [FormularioEnsayoController::class, 'update'])->name('update');
        Route::post('/resultados/test', [FormularioEnsayoResultadoController::class, 'testAdmin'])->name('store.test');
    });
    Route::prefix('/grupos-selectores')->name('admin.grupos-selectores.')->group(function () {
        Route::get('/buscar', [GrupoSelectorController::class, 'buscar'])->name('buscar');
        Route::post('/guardar', [GrupoSelectorController::class, 'guardar'])->name('guardar');
        Route::delete('/eliminar/{id}', [GrupoSelectorController::class, 'eliminar'])->name('eliminar');
        Route::get('/opciones/{id}', [GrupoSelectorController::class, 'getOpciones'])->name('opciones');
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('ensayos/{ensayo}/grupos', [GrupoSelectorController::class, 'index'])
            ->name('grupos.index');
        Route::post('ensayos/{ensayo}/grupos', [GrupoSelectorController::class, 'store'])->name('grupos.store');
        Route::get('/json/ensayos/{ensayoId}/grupos', [GrupoSelectorController::class, 'gruposSelectoresJson'])->name('grupos.selectores.json');
        Route::put('grupos/{grupo}', [GrupoSelectorController::class, 'update'])->name('grupos.update');
        Route::delete('grupos/{grupo}', [GrupoSelectorController::class, 'destroy'])->name('grupos.destroy');

        Route::post('grupos/{grupo}/opciones', [OpcionSelectorController::class, 'store'])->name('opciones.store');
        Route::put('opciones/{opcion}', [OpcionSelectorController::class, 'update'])->name('opciones.update');
        Route::delete('opciones/{opcion}', [OpcionSelectorController::class, 'destroy'])->name('opciones.destroy');
    });

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
    Route::get('/configuracion/certificado', [ConfiguracionController::class, 'certificados'])->name('configuracion.cerfificado');

    Route::resource('formularios', FormularioController::class);

    Route::get('/desempeno/detalle-certificado/data', [InscripcionPaqueteController::class, 'certificadoDesempenoIndex'])->name('certificado-desempeno.index');
    Route::get('/desempeno/detalle-certificado/ajax/{id}/data', [InscripcionPaqueteController::class,  'certificadoDesempenoAjaxIndex'])->name('certificado-desempeno.ajax.index');
    Route::get('/certificados-desempeno/ea/{id}/labs', [InscripcionPaqueteController::class,  'certificadoDesempenoListLabs'])->name('certificados.desempeno.labs.show');

    Route::get('/certificados/desempeno-participacion', [CertificadoController::class,  'certificadoDesempenoParticionListLabs'])->name('list.cert.participacion.desemp');

    Route::post('/certificados/publicar/{gestion}', [CertificadoController::class,  'publicarCertificado'])->name('certificados.publicar');
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
    Route::put('/inscripcion/{id}/anular', [LabController::class, 'anularInscripcion'])->name('inscripciones.anular');

    Route::get('/certificados', [LabController::class, 'certificadosDisponibles'])->name('lab.certificados.disponibles.index');
    Route::get('/certificados/ajax/data', [LabController::class, 'getCertificadosDisponibleData'])->name('certificados.ajax.data');
    Route::get('/certificados/participacion/{gestion}', [LabController::class, 'certificadoPartificacionPDF'])->name('lab.certificados.participacion.pdf');
    Route::get('/certificados/desemp/{gestion}', [LabController::class, 'certificadoDesempPDF'])->name('certificados.desemp.pdf');

    Route::post('inscripcion-paquetes/{id}/documentos', [DocumentosController::class, 'subirDocumentosInscripcion'])
        ->name('inscripcion-paquetes.lab.subirDocumentos');
    Route::post('inscripcion-paquetes/{id}/pago-documentos', [DocumentosController::class, 'subirComprobante'])
        ->name('pago.lab.subirComprobante');
    Route::get('/inscripciones/{id}/comprobantes', [DocumentosController::class, 'indexDocPagos'])
        ->name('documentos.pagos.index');

    Route::get('/inscripcion-ensayos', [RegistroResultadosController::class, 'listaEnsayosInscritos'])->name('lab.inscritos-ensayos.index');
    Route::get('/inscripcion-ensayos/{id}/formularios', [RegistroResultadosController::class, 'getFormulariosByEa'])->name('lab.inscritos-ensayos.formularios');
    Route::get('/inscripcion-ensayos/formulario/{id}/llenar/{idEA}', [RegistroResultadosController::class, 'formularioLlenar'])->name('lab.inscritos-ensayos.formularios.llenar');
    Route::post('/inscripcion-ensayos/formulario/{id}/llenar', [RegistroResultadosController::class, 'guardarResultados'])->name('laboratorio.formularios.guardar');
    
    Route::post('/formularios/resultados', [FormularioEnsayoResultadoController::class, 'store'])->name('lab.resultados.store');
});

Route::middleware(['auth', 'usuario.activo'])->prefix('responsable')->group(function () {
    Route::get('/ea/{id}/labs', [ResponsableLaboratorioController::class, 'index'])->name('ea.lab.inscritos');
    Route::get('/ea/{id}/certificados', [ResponsableLaboratorioController::class, 'showUploadCertificado'])->name('ea.lab.certificados');
    Route::post('/ea/{id}/subir_ponderaciones', [ResponsableLaboratorioController::class, 'uploadCertificadoData'])->name('ea.lab.subir.ponderaciones');
    Route::get('/ea/{id}/en_revision', [ResponsableLaboratorioController::class, 'getLaboratoriosDesempenoTemporal'])->name('ea.lab.desempeno.temporal');
    Route::get('/ea/{id}/labs-ajax', [ResponsableLaboratorioController::class, 'getData'])->name('ea.lab.inscritos.ajax');
    Route::post('/detalle-certificado/{id}', [DetalleCertificadoController::class, 'update'])->name('detalle-certificado.update');
    Route::post('/ea/{id}/certificado/confirmar', [ResponsableLaboratorioController::class, 'confirmarDatosCertificados'])->name('confirmar.datos.certificados');
    Route::get('/ea/{id}/certificado/confirmados', [ResponsableLaboratorioController::class, 'getLaboratoriosDesempenoConfirmados'])->name('ea.lab.desempeno.confirmado');
    Route::get('/ea/gestion-formularios', [GestionFormulariosController::class, 'index'])->name('ea.formulario.index');
    Route::get('/ea/{id}/gestion-formularios/labs', [GestionFormulariosController::class, 'labs'])->name('ea.formulario.lab.inscritos');
    Route::get('/ea/{id}/gestion-formularios/labs/data', [GestionFormulariosController::class, 'getData'])->name('ea.formulario.lab.inscritos.getData');
});

require __DIR__ . '/auth.php';
