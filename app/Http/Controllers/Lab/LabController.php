<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PdfInscripcionController;
use App\Mail\EnvioCodigoLab;
use App\Mail\LaboratorioVerificacionDatos;
use App\Models\CategoriaLaboratorio;
use App\Models\Certificado;
use App\Models\Configuracion;
use App\Models\Departamento;
use App\Models\Inscripcion;
use App\Models\Laboratorio;
use App\Models\LaboratorioTem;
use App\Models\Municipio;
use App\Models\NivelLaboratorio;
use App\Models\Pais;
use App\Models\Permiso;
use App\Models\Programa;
use App\Models\Provincia;
use App\Models\TipoLaboratorio;
use App\Models\User;
use App\Notifications\VerificarCorreoLab;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LabController extends Controller
{


    public function profile()
    {
        Gate::authorize(Permiso::LABORATORIO);
        $user = Auth::user();
        $laboratorio = $user->laboratorio;
        $backTo = false;
        return view('laboratorio.show', compact('laboratorio', 'backTo'));
    }


    public function editarProfile()
    {
        Gate::authorize(Permiso::LABORATORIO);
        $user = Auth::user();
        $laboratorio = $user->laboratorio;
        $backTo = false;
        return view('laboratorio.edit', [
            'laboratorio' => $laboratorio,
            'paises' => Pais::active()->get(),
            'niveles' => NivelLaboratorio::all(),
            'tipos' => TipoLaboratorio::all(),
            'departamentos' => $laboratorio->pais->departamentos()->orderBy('nombre_dep')->get(),
            'provincias' => $laboratorio->departamento->provincias()->orderBy('nombre_prov')->get(),
            'municipios' => $laboratorio->provincia->municipios()->orderBy('nombre_municipio')->get(),
            'categorias' => CategoriaLaboratorio::all(),
            'backTo' => $backTo
        ]);
    }

    public function generarContrato()
    {
        Gate::authorize(Permiso::LABORATORIO);
        $lab = Auth::user()->laboratorio;
        $inscripcion = $lab->inscripciones()->where('gestion', configuracion(Configuracion::GESTION_INSCRIPCION))->firstOrFail();
        $pdfController = app(PdfInscripcionController::class);
        return $pdfController->generarContrato($inscripcion->id);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize(Permiso::LABORATORIO);
        $user = Auth::user();
        $laboratorio = $user->laboratorio;
        $inscripciones = $laboratorio->inscripciones()->get();
        return view('laboratorio.inscripciones', [
            'paises' => [],
            'niveles' => [],
            'tipos' => [],
            'departamentos' => [],
            'provincias' => [],
            'municipios' => [],
            'categorias' => [],
            'gestiones' => configuracion(Configuracion::KEY_GESTION_FILTER),
        ]);
    }

    public function getInscripcionData()
    {
        $lab = Auth::user()->laboratorio;
        $query = Inscripcion::with([
            'laboratorio.pais',
            'laboratorio.tipo',
            'laboratorio.categoria',
            'laboratorio.nivel',
            'detalleInscripciones'
        ])->where('id_lab', $lab->id);

        return datatables()
            ->of($query)
            ->addColumn('nombre_lab', fn($i) => $i->laboratorio->nombre_lab ?? '-')
            ->addColumn('codigo_lab', fn($i) => $i->laboratorio->cod_lab ?? '-')
            ->addColumn('pais', fn($i) => $i->laboratorio->pais->nombre_pais ?? '-')
            ->addColumn('tipo', fn($i) => $i->laboratorio->tipo->descripcion ?? '-')
            ->addColumn('categoria', fn($i) => $i->laboratorio->categoria->descripcion ?? '-')
            ->addColumn('nivel', fn($i) => $i->laboratorio->nivel->descripcion_nivel ?? '-')
            ->addColumn('fecha', fn($i) => $i->fecha_inscripcion)
            ->addColumn('gestion', fn($i) => $i->gestion)
            ->addColumn('paquetes', fn($i) => $i->detalleInscripciones->pluck('descripcion_paquete')->implode(', '))
            ->addColumn('costo', fn($i) => number_format($i->costo_total, 2) . ' Bs.')
            ->addColumn('estado', fn($i) => $i->getStatusInscripcion())
            ->addColumn('cuenta', fn($i) => $i->getStatusCuenta())
            ->addColumn('acciones', function ($i) {
                return view('laboratorio.inscripcion.action-btn', [
                    'showUrl' => route('lab.inscripcion.show', $i->id),
                    'boletaPdf' => route('formulario_inscripcion', $i->id),
                    'inscripcion' => $i,
                ])->render();
            })
            ->rawColumns(['estado', 'cuenta', 'acciones'])
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function labInscripcion()
    {

        $user = Auth::user();
        $laboratorio = $user->laboratorio;
        $tipoLabId = $laboratorio->id_tipo;

        $programas = Programa::active()
            ->whereHas('areas.paquetes.tiposLaboratorios', function ($query) use ($tipoLabId) {
                $query->where('tipo_laboratorio_id', $tipoLabId);
            })
            ->get();
        $deudasPendientes = $laboratorio->deudasPendientes();
        $redirectTo =  route('lab.ins.index');
        return view('inscripcion_paquete.create', compact('laboratorio', 'programas', 'redirectTo', 'deudasPendientes'));
    }


    public function labShowInscripcion($id)
    {
        $user = Auth::user();
        $laboratorio = $user->laboratorio;

        // Asegurarse que la inscripción pertenezca al laboratorio autenticado
        $inscripcion = $laboratorio->inscripciones()
            ->with(['laboratorio', 'detalleInscripciones', 'pagos' => function ($query) {
                $query->where('status', 1);
            }, 'documentos', 'vigencia'])
            ->findOrFail($id);
        $programas = Programa::active()->get();
        $backTo = route('lab.ins.index');

        return view('inscripcion_paquete.show', compact('inscripcion', 'backTo'));
    }

    public function certificadosDisponibles()
    {
        Gate::authorize(Permiso::LABORATORIO);
        $user = Auth::user();
        $laboratorio = $user->laboratorio;
        $inscripciones = $laboratorio->inscripciones()
            ->Aprobado()
            ->whereHas('certificado', function ($query) {
                $query->Publicado();
            })
            ->with(['certificado.detalles'])
            ->get()
            ->map(function ($inscripcion) {
                $tiene = $inscripcion->certificado
                    ->detalles
                    ->contains(function ($detalle) {
                        return !is_null($detalle->calificacion_certificado);
                    });
                $inscripcion->tiene_certificado_desempeno = $tiene;
                return $inscripcion;
            });

        $certificadosDisponibles = $inscripciones
            ->groupBy('gestion')
            ->map(function ($inscripciones) {
                $tieneDesempeno = $inscripciones->contains(function ($inscripcion) {
                    return $inscripcion->tiene_certificado_desempeno;
                });

                return (object) [
                    'inscripciones' => $inscripciones,
                    'tiene_certificado_desempeno' => $tieneDesempeno,
                    'codigo' => $inscripciones->first()->laboratorio->cod_lab ?? ''
                ];
            });
        return view('certificados.lab.certificados_disponibles', compact('certificadosDisponibles'));
    }

    public function getCertificadosDisponibleData() {}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('lab_tem.create', [
            'paises' => Pais::active()->get(),
            'niveles' => NivelLaboratorio::all(),
            'tipos' => TipoLaboratorio::all(),
            'departamentos' => [],
            'provincias' => [],
            'municipios' => [],
            'categorias' => CategoriaLaboratorio::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function registrar(Request $request)
    {
        $request->validate([
            'numsedes_lab' => 'nullable|string|max:15',
            'nombre_lab' => 'required|string|max:100',
            'sigla_lab' => 'nullable|string|max:20|unique:laboratorio,sigla_lab',
            'nit_lab' => 'nullable|numeric',
            'id_nivel' => 'required|exists:nivel_laboratorio,id',
            'id_tipo' => 'required|exists:tipo_laboratorio,id',
            'id_categoria' => 'required|exists:categoria,id',
            'respo_lab' => 'required|string|max:50',
            'ci_respo_lab' => 'nullable|string|max:12',
            'repreleg_lab' => 'required|string|max:50',
            'ci_repreleg_lab' => 'nullable|string|max:12',
            'id_pais' => 'required|exists:pais,id',
            'id_dep' => 'required|exists:departamento,id',
            'id_prov' => 'required|exists:provincia,id',
            'id_municipio' => 'required|exists:municipio,id',
            'zona_lab' => 'required|string|max:50',
            'direccion_lab' => 'required|string|max:150',
            'wapp_lab' => 'required|numeric',
            'wapp2_lab' => 'nullable|numeric',
            'mail_lab' => 'required|email|unique:users,email',
            'mail2_lab' => 'nullable|email',
            'telefono' => 'nullable|string|max:50',
            'password' => 'required|string|min:8|confirmed',
        ], $this->messages());

        $laboratorio = LaboratorioTem::create([
            'numsedes_lab' => $request->numsedes_lab,
            'nit_lab' => $request->nit_lab,
            'nombre_lab' => $request->nombre_lab,
            'sigla_lab' => $request->sigla_lab,
            'id_nivel' => $request->id_nivel,
            'id_tipo' => $request->id_tipo,
            'id_categoria' => $request->id_categoria,
            'respo_lab' => $request->respo_lab,
            'ci_respo_lab' => $request->ci_respo_lab,
            'repreleg_lab' => $request->repreleg_lab,
            'ci_repreleg_lab' => $request->ci_repreleg_lab,
            'id_pais' => $request->id_pais,
            'id_dep' => $request->id_dep,
            'id_prov' => $request->id_prov,
            'id_municipio' => $request->id_municipio,
            'zona_lab' => $request->zona_lab,
            'direccion_lab' => $request->direccion_lab,
            'wapp_lab' => $request->wapp_lab,
            'wapp2_lab' => $request->wapp2_lab,
            'mail_lab' => $request->mail_lab,
            'mail2_lab' => $request->mail2_lab,
            'telefono' => $request->telefono,
            'password' => $request->password,
        ]);
        $pais = Pais::find($request->id_pais)?->nombre ?? 'Desconocido';
        $departamento = Departamento::find($request->id_dep)?->nombre_dep ?? 'Desconocido';
        $provincia = Provincia::find($request->id_prov)?->nombre_prov ?? 'Desconocido';
        $municipio = Municipio::find($request->id_municipio)?->nombre_municipio ?? 'Desconocido';
        $categoria = CategoriaLaboratorio::find($request->id_categoria)?->descripcion ?? 'Desconocido';
        $tipo = TipoLaboratorio::find($request->id_tipo)?->descripcion ?? 'Desconocido';
        $nivel = NivelLaboratorio::find($request->id_nivel)?->descripcion_nivel ?? 'Desconocido';

        try {
            Mail::to($laboratorio->mail_lab)->send(
                new LaboratorioVerificacionDatos(
                    $laboratorio,
                    $pais,
                    $departamento,
                    $provincia,
                    $municipio,
                    $categoria,
                    $tipo,
                    $nivel
                )
            );
        } catch (\Throwable $th) {

            return redirect('login')->with('notice', [
                'title' => 'Registro exitoso, pero...',
                'message' => <<<HTML
                <div style="text-align: left; font-size: 14px; line-height: 1.5;">
                    Tu laboratorio fue registrado correctamente, 
                    <b>pero no pudimos enviar el correo de verificación</b> a:
                    <br><br>
                    <span style="display: inline-block; margin-top: 6px; font-weight: bold; color: #dc2626; font-size: 15px;">
                        {$laboratorio->mail_lab}
                    </span>
                    <br><br>
                    ⚠️ Para continuar, por favor <b>ingresa nuevamente tus datos</b> 
                    y asegúrate de escribir un correo válido y activo.
                </div>
                HTML,
                'type' => 'warning',
            ]);
        }

        return redirect('login')->with('notice', [
            'title' => 'Registro exitoso',
            'message' =>  <<<HTML
                <div style="text-align: left; font-size: 14px; line-height: 1.5;">
                    Tu laboratorio ha sido <b>registrado temporalmente</b>. 
                    Para <b>finalizar y oficializar tu registro</b>, debes verificar tus datos mediante el enlace que enviamos a tu correo. 
                    <br><br>
                    📩 Hemos enviado el correo de verificación a: 
                    <span style="display: inline-block; margin-top: 6px; font-weight: bold; color: #1d4ed8; font-size: 15px;">
                        {$laboratorio->mail_lab}
                    </span>
                    <br><br>
                    👉 Revisa tu bandeja de entrada o la carpeta de <i>spam</i>.
                </div>
            HTML,
            'type' => 'success',
        ]);
    }


    private function messages()
    {
        return [
            'cod_lab.required' => 'El código del laboratorio es obligatorio.',
            'cod_lab.unique' => 'El código del laboratorio ya está en uso.',
            'antcod_peec.max' => 'El código PEEC no debe exceder 10 caracteres.',
            'numsedes_lab.max' => 'El número de sedes no debe exceder 15 caracteres.',
            'nombre_lab.required' => 'El nombre del laboratorio es obligatorio.',
            'sigla_lab.unique' => 'La sigla del laboratorio ya está en uso.',
            'nit_lab.required' => 'El NIT del laboratorio es obligatorio.',
            'nit_lab.unique' => 'El NIT del laboratorio ya está en uso.',
            'id_nivel.required' => 'Debe seleccionar un nivel de laboratorio.',
            'id_nivel.exists' => 'El nivel seleccionado no es válido.',
            'id_tipo.required' => 'Debe seleccionar un tipo de laboratorio.',
            'id_tipo.exists' => 'El tipo seleccionado no es válido.',
            'id_categoria.required' => 'Debe seleccionar una categoría.',
            'id_categoria.exists' => 'La categoría seleccionada no es válida.',
            'respo_lab.required' => 'El nombre del responsable es obligatorio.',
            'ci_respo_lab.max' => 'El CI del responsable no debe exceder 12 caracteres.',
            'repreleg_lab.required' => 'El nombre del representante legal es obligatorio.',
            'ci_repreleg_lab.max' => 'El CI del representante legal no debe exceder 12 caracteres.',
            'id_pais.required' => 'Debe seleccionar un país.',
            'id_pais.exists' => 'El país seleccionado no es válido.',
            'id_dep.required' => 'Debe seleccionar un departamento.',
            'id_dep.exists' => 'El departamento seleccionado no es válido.',
            'id_prov.required' => 'Debe seleccionar una provincia.',
            'id_prov.exists' => 'La provincia seleccionada no es válida.',
            'id_municipio.required' => 'Debe seleccionar un municipio.',
            'id_municipio.exists' => 'El municipio seleccionado no es válido.',
            'zona_lab.required' => 'La zona es obligatoria.',
            'direccion_lab.required' => 'La dirección es obligatoria.',
            'wapp_lab.required' => 'El número de WhatsApp principal es obligatorio.',
            'wapp_lab.numeric' => 'El número de WhatsApp principal debe ser numérico.',
            'wapp2_lab.numeric' => 'El número de WhatsApp secundario debe ser numérico.',
            'mail_lab.required' => 'El correo electrónico principal es obligatorio.',
            'mail_lab.email' => 'El correo electrónico principal debe ser válido.',
            'mail_lab.unique' => 'El correo electrónico ya está registrado.',
            'mail2_lab.email' => 'El correo electrónico secundario debe ser válido.',
            'telefono.max' => 'El teléfono no debe exceder 50 caracteres.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ];
    }

    public function confirmarDatos($id)
    {
        $labTem = LaboratorioTem::find($id);
        if (!$labTem) {
            $labTemWithTrashed = LaboratorioTem::withTrashed()->findOrFail($id);
            $email = $labTemWithTrashed->mail_lab;
            $user = User::where('email', $email)->first();
            $message = <<<HTML
                <div style="text-align: left; font-size: 14px; line-height: 1.5;">
                    ⚠️ Este laboratorio ya fue <b>confirmado anteriormente</b>.
                    <br><br>
                    Tu <b>código de usuario asignado</b> es:
                    <span style="display:inline-block; font-weight:bold; color:#1d4ed8; font-size:15px;">
                        {$user->username}
                    </span>
                    <br><br>
                    Usa este código para <b>iniciar sesión</b> junto con tu contraseña.
                </div>
                HTML;
            return redirect('login')->with('notice', [
                'title' => 'Laboratorio ya confirmado',
                'message' => $message,
                'type' => 'info',
            ]);
        }

        $pais = Pais::find($labTem->id_pais);
        $sigla = strtoupper($pais->sigla_pais); // Ej: BOL

        // Generar username incremental
        $count = User::where('username', 'LIKE', "$sigla%")->count() + 1;
        $username = $sigla . str_pad($count, 4, '0', STR_PAD_LEFT);
        try {
            DB::beginTransaction();

            // Crear usuario
            $user = User::create([
                'nombre' => $labTem->nombre_lab,
                'username' => $username,
                'email' => $labTem->mail_lab,
                'password' => $labTem->password,
                'status' => false,
                'ap_paterno' => 'Laboratorio_ap_paterno', // Asignar un apellido por defecto
                'ci' => $labTem->ci_respo_lab ?: '00000000',
                'telefono' => $labTem->telefono,
                'email_verified_at' => now(),
                'status' => true
            ]);

            // Crear laboratorio
            $lab = Laboratorio::create([
                'id_usuario' => $user->id,
                'cod_lab' => $username,
                'antcod_peec' => $username,
                'numsedes_lab' => $labTem->numsedes_lab,
                'nit_lab' => $labTem->nit_lab,
                'nombre_lab' => $labTem->nombre_lab,
                'sigla_lab' => $labTem->sigla_lab,
                'id_nivel' => $labTem->id_nivel,
                'id_tipo' => $labTem->id_tipo,
                'id_categoria' => $labTem->id_categoria,
                'respo_lab' => $labTem->respo_lab,
                'ci_respo_lab' => $labTem->ci_respo_lab,
                'repreleg_lab' => $labTem->repreleg_lab,
                'ci_repreleg_lab' => $labTem->ci_repreleg_lab,
                'id_pais' => $labTem->id_pais,
                'id_dep' => $labTem->id_dep,
                'id_prov' => $labTem->id_prov,
                'id_municipio' => $labTem->id_municipio,
                'zona_lab' => $labTem->zona_lab,
                'direccion_lab' => $labTem->direccion_lab,
                'wapp_lab' => $labTem->wapp_lab,
                'wapp2_lab' => $labTem->wapp2_lab,
                'mail_lab' => $labTem->mail_lab,
                'mail2_lab' => $labTem->mail2_lab,
                'status' => true,
                'created_by' => $user->id,
                'updated_by' =>  $user->id,
            ]);
            $labTem->delete();
            DB::commit();
            try {
                Mail::to($user->email)->send(new EnvioCodigoLab($user, $lab));
                $message = <<<HTML
                <div style="text-align: left; font-size: 14px; line-height: 1.5;">
                    ✅ <b>Laboratorio confirmado y registrado exitosamente.</b>
                    <br><br>
                    Tu <b>código de usuario asignado</b> es: 
                    <span style="display:inline-block; font-weight:bold; color:#1d4ed8; font-size:15px;">$username</span>
                    <br><br>
                    Puedes usar este código para <b>iniciar sesión</b> junto con tu contraseña.
                    <br><br>
                    Hemos enviado un correo a <b>{$user->email}</b> con los detalles de verificación.
                </div>
                HTML;

                return redirect('login')->with('notice', [
                    'title' => 'Registro confirmado',
                    'message' => $message,
                    'type' => 'success',
                ]);
            } catch (\Throwable $th) {
                \Log::error('Error al enviar el correo de confirmación: ' . $th->getMessage());
                $message = <<<HTML
                    <div style="text-align: left; font-size: 14px; line-height: 1.5;">
                        ✅ <b>Laboratorio confirmado y registrado correctamente.</b> 
                        <br>
                        ⚠️ <b>No se pudo enviar el correo de confirmación a</b> 
                        <span style="font-weight:bold; color:#dc2626;">{$user->email}</span>.
                        <br>
                        Sin embargo, tu cuenta ya fue creada en el sistema y puedes acceder directamente con:
                        <br>
                        <b>Usuario/codigo (guárdalo bien):</b> <span style="font-weight:bold; color:#1d4ed8;">$username</span>
                        <br>
                        <b>Contraseña:</b> la que registraste durante la inscripción.
                        <br>
                        📌 <b>Importante:</b> Anota y guarda tu <span style="color:#1d4ed8; font-weight:bold;">usuario/código</span> en un lugar seguro, ya que lo necesitarás siempre para ingresar al sistema.
                    </div>
                HTML;
                return redirect('login')->with('notice', [
                    'title' => 'Registro confirmado, pero...',
                    'message' => $message,
                    'type' => 'warning',
                ]);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error al iniciar transacción: ' . $e->getMessage());
            return redirect('login')->with('notice', [
                'title' => 'Error',
                'message' => 'Ocurrió un error al registrar la información. Por favor, inténtelo nuevamente.',
                'type' => 'error',
            ]);
        }
    }

    public function generarFormularioIns(Request $request, $id)
    {
        Gate::authorize(Permiso::LABORATORIO);
        $lab = Auth::user()->laboratorio;
        $inscripcion = $lab->inscripciones()->findOrFail($id);
        $pdfController = app(PdfInscripcionController::class);
        return $pdfController->generar($inscripcion->id);
    }

    public function anularInscripcion(Request $request, $id)
    {
        Gate::authorize(Permiso::LABORATORIO);
        $lab = Auth::user()->laboratorio;
        $inscripcion = $lab->inscripciones()->findOrFail($id);
        $inscripcion->status_inscripcion = Inscripcion::STATUS_ANULADO;
        $inscripcion->updated_by = Auth::user()->id;
        $inscripcion->save();
        return back()->with('success', 'Se anulo  su  Inscripcion.');
    }

    public function certificadoDesempPDF($gestion)
    {
        Gate::authorize(Permiso::LABORATORIO);
        $user = Auth::user();
        $laboratorio = $user->laboratorio;
        $inscripciones = $laboratorio->inscripciones()
            ->Aprobado()
            ->whereHas('certificado', fn($query) => $query->Publicado())
            ->where('gestion', $gestion)
            ->whereHas('certificado.detalles', fn($query) => $query->whereNotNull('calificacion_certificado'))
            ->with(['certificado.detalles'])
            ->get();
        if ($inscripciones->isEmpty()) {
            return redirect('/')
                ->with('info', '⚠️ No se encontraron certificados registrados para la gestión seleccionada.');
        }
        $dataPorArea = [];
        $codigoCertificado = '';
        foreach ($inscripciones as $inscripcion) {
            $certificado = $inscripcion->certificado;
            $detalles = $certificado->detalles;

            if ($detalles->isEmpty()) continue;

            foreach ($detalles as $detalle) {
                if (is_null($detalle->calificacion_certificado)) continue;

                if (!isset($dataPorArea["$detalle->detalle_area"])) {
                    $dataPorArea["$detalle->detalle_area"] = [
                        'certificado' => $certificado,
                        'detalles' => []
                    ];
                }

                $dataPorArea["$detalle->detalle_area"]['detalles'][] = [
                    'ensayo' => $detalle->detalle_ea,
                    'ponderacion' => $detalle->calificacion_certificado,
                ];
                $codigoCertificado = $inscripcion->id;
            }
        }
        $url = route('verificar.certificado', ['code' => $codigoCertificado, 'type' => Certificado::TYPE_DESEMP]);
        $qr = base64_encode(
            QrCode::format('png')->size(400)->margin(1)->generate($url)
        );
        // return view('certificados.pdf.desemp', ['data' => $dataPorArea, 'qr'=>$qr]); 
        $pdf = Pdf::loadView('certificados.pdf.desemp', ['data' => $dataPorArea, 'qr' => $qr])
            ->setPaper('A4', 'portrait');
        $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);

        $response = $pdf->stream('certificados-desempeno.pdf');
        return $response;
    }

    public function certificadoPartificacionPDF($gestion)
    {
        Gate::authorize(Permiso::LABORATORIO);
        $user = Auth::user();
        $laboratorio = $user->laboratorio;

        $query = $laboratorio->inscripciones()
            ->Aprobado()
            ->whereHas('certificado', fn($query) => $query->Publicado())
            ->where('gestion', $gestion);

        $ins = $query->with('certificado')
            ->first();
        $certificado = $ins->certificado;
        $codigoCertificado = $ins->id;
        $query = $laboratorio->inscripciones()
            ->Aprobado()
            ->whereHas('certificado', fn($query) => $query->Publicado())
            ->where('gestion', $gestion);
        $ensayosA = $query
            ->whereHas('certificado.detalles')
            ->with(['certificado.detalles'])
            ->get()
            ->pluck('certificado.detalles')
            ->flatten()
            ->pluck('detalle_ea')
            ->implode(', ');
        $url = route('verificar.certificado', ['code' => $codigoCertificado, 'type' => Certificado::TYPE_PARTICIPACION]);
        $qr = base64_encode(
            QrCode::format('png')->size(220)->margin(1)->generate($url)
        );
        $pdf = Pdf::loadView('certificados.pdf.participacion', ['ensayosA' => $ensayosA, 'certificado' => $certificado, 'qr' => $qr])
            ->setPaper('A4', 'portrait');
        $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
        return $pdf->stream('certificados-particiapcion.pdf');
    }
}
