<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\VerificacionCorreoLaboratorio;
use App\Models\CategoriaLaboratorio;
use App\Models\Departamento;
use App\Models\Laboratorio;
use App\Models\NivelLaboratorio;
use App\Models\Pais;
use App\Models\Permiso;
use App\Models\TipoLaboratorio;
use App\Models\User;
use App\Notifications\VerificarCorreoLab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class LaboratorioController extends Controller
{
    public function __construct()
    {
        $this->middleware('canany:' . Permiso::GESTION_LABORATORIO . ',' . Permiso::ADMIN)->only(['create', 'store', 'show', 'edit', 'destroy']);
    }

    public function index()
    {
        if (!Gate::any([Permiso::GESTION_LABORATORIO, Permiso::ADMIN, Permiso::GESTION_INSCRIPCIONES])) {
            return redirect('/')->with('error', 'No tiene autorización para acceder a esta sección.');
        }
        $laboratorios = Laboratorio::with('usuario')->latest()->paginate(10);
        // Cargar países para el filtro
        $paises = Pais::active()->get();
        $tipos = TipoLaboratorio::active()->get();
        $categorias = CategoriaLaboratorio::active()->get();
        $niveles = NivelLaboratorio::active()->get();
        return view('laboratorio.index', compact('laboratorios', 'paises', 'tipos', 'categorias', 'niveles'));
    }

    public function create()
    {

        return view('laboratorio.create', [
            'paises' => Pais::active()->get(),
            'niveles' => NivelLaboratorio::all(),
            'tipos' => TipoLaboratorio::all(),
            'departamentos' => [],
            'provincias' => [],
            'municipios' => [],
            'categorias' => CategoriaLaboratorio::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'antcod_peec' => 'nullable|string|max:10',
            'numsedes_lab' => 'nullable|string|max:15',
            'nombre_lab' => 'required|string|max:100',
            'sigla_lab' => 'nullable|string|max:20',
            'nit_lab' => 'required|numeric',
            'id_nivel' => 'required|exists:nivel_laboratorio,id',
            'id_tipo' => 'required|exists:tipo_laboratorio,id',
            'id_categoria' => 'required|exists:categoria,id',
            'respo_lab' => 'required|string|max:50',
            'ci_respo_lab' => 'required|string|max:15',
            'repreleg_lab' => 'required|string|max:50',
            'ci_repreleg_lab' => 'required|string|max:15',
            'id_pais' => 'required|exists:pais,id',
            'id_dep' => 'required|exists:departamento,id',
            'id_prov' => 'required|exists:provincia,id',
            'id_municipio' => 'required|exists:municipio,id',
            'zona_lab' => 'required|string|max:50',
            'wapp_lab' => 'required|numeric',
            'wapp2_lab' => 'nullable|numeric',
            'mail_lab' => 'required|email|unique:users,email',
            'mail2_lab' => 'nullable|email',
            'telefono' => 'nullable|string|max:50',
            'password' => 'required|string|min:8|confirmed',
        ], $this->messages());
        $direccion = $request->calle_lab . '||' . $request->numero_lab . '||' . $request->referencia;
        $pais = Pais::find($request->id_pais);
        $sigla = strtoupper($pais->sigla_pais); // Ej: BOL

        // Generar username incremental
        // $count = User::where('username', 'LIKE', "$sigla%")->count() + 1;
        // $username = $sigla . str_pad($count, 4, '0', STR_PAD_LEFT);
        // Crear usuario
        try {
            DB::beginTransaction();
            $lastUser = User::where('username', 'LIKE', "$sigla%")
                ->lockForUpdate()
                ->orderByDesc('username')
                ->first();

            if ($lastUser) {
                $lastNumber = (int) substr($lastUser->username, strlen($sigla));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }

            $username = $sigla . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            $user = User::create([
                'nombre' => $request->nombre_lab,
                'username' => $username,
                'email' => $request->mail_lab,
                'password' => $request->password,
                'status' => true,
                'ap_paterno' => 'Laboratorio_ap_paterno', // Asignar un apellido por defecto
                'ci' => $request->ci_respo_lab ?: '00000000',
                'telefono' => $request->telefono,
                'email_verified_at' => now()
            ]);

            // Crear laboratorio
            $lab = Laboratorio::create([
                'id_usuario' => $user->id,
                'cod_lab' => $username,
                'antcod_peec' => $username,
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
                'direccion_lab' => $direccion,
                'wapp_lab' => $request->wapp_lab,
                'wapp2_lab' => $request->wapp2_lab,
                'mail_lab' => $request->mail_lab,
                'mail2_lab' => $request->mail2_lab,
                'status' => true,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
            DB::commit();
            try {
                // $user->notify(new VerificarCorreoLab($user, $lab));
            } catch (\Throwable $th) {
                // return redirect()->route('laboratorio.index')->with('warning', 'Laboratorio registrado correctamente, pero no se pudo enviar el correo de verificación.');
            }
            return redirect()->route('laboratorio.index')->with('success', 'Laboratorio registrado correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error al iniciar transacción: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Lo sentimos, no se pudo registrar el laboratorio. Inténtelo de nuevo en unos momentos.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $laboratorio = Laboratorio::with([
            'usuario',
            'pais',
            'departamento',
            'provincia',
            'municipio',
            'tipo',
            'categoria',
            'nivel'
        ])->findOrFail($id);

        return view('laboratorio.show', compact('laboratorio'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Laboratorio $laboratorio)
    {
        return view('laboratorio.edit', [
            'laboratorio' => $laboratorio,
            'paises' => Pais::active()->get(),
            'niveles' => NivelLaboratorio::active()->get(),
            'tipos' => TipoLaboratorio::active()->get(),
            'departamentos' => $laboratorio->pais?->departamentos()->orderBy('nombre_dep')->get() ?? collect(),
            'provincias' => $laboratorio->departamento?->provincias()->orderBy('nombre_prov')->get() ?? collect(),
            'municipios' => $laboratorio->provincia?->municipios()->orderBy('nombre_municipio')->get() ?? collect(),
            'categorias' => CategoriaLaboratorio::active()->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $laboratorio = Laboratorio::findOrFail($id);
        $user = $laboratorio->usuario;

        $request->validate([
            'cod_lab' => 'nullable|string|max:20|unique:laboratorio,cod_lab,' . $laboratorio->id,
            'antcod_peec' => 'nullable|string|max:10',
            'numsedes_lab' => 'nullable|string|max:15',
            'nombre_lab' => 'required|string|max:100',
            'sigla_lab' => 'nullable|string|max:20',
            'nit_lab' => 'required|numeric',
            'id_nivel' => 'required|exists:nivel_laboratorio,id',
            'id_tipo' => 'required|exists:tipo_laboratorio,id',
            'id_categoria' => 'required|exists:categoria,id',
            'respo_lab' => 'required|string|max:50',
            'ci_respo_lab' => 'nullable|string|max:15',
            'repreleg_lab' => 'required|string|max:50',
            'ci_repreleg_lab' => 'nullable|string|max:15',
            'id_pais' => 'required|exists:pais,id',
            'id_dep' => 'required|exists:departamento,id',
            'id_prov' => 'required|exists:provincia,id',
            'id_municipio' => 'required|exists:municipio,id',
            'zona_lab' => 'required|string|max:50',
            'wapp_lab' => 'required|numeric',
            'wapp2_lab' => 'nullable|numeric',
            'mail_lab' => 'required|email|unique:users,email,' . $user->id,
            'mail2_lab' => 'nullable|email',
            'telefono' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'nullable|boolean',
        ], $this->messages());
        $user->nombre = $request->nombre_lab;
        $user->telefono = $request->telefono;
        $user->ci = $request->ci_respo_lab ?: '00000000';
        $direccion = $request->calle_lab . '||' . $request->numero_lab . '||' . $request->referencia;

        if ($request->password) {
            $user->password = $request->password;
        }

        if ($request->email_verified_at && !$user->email_verified_at) {
            $user->email_verified_at = now();
        }

        if (Gate::any(abilities: [Permiso::GESTION_LABORATORIO, Permiso::ADMIN])) {
            if ($request->mail_lab != $laboratorio->mail_lab) {
                $laboratorio->mail_lab = $request->mail_lab;
                // $user->email_verified_at = null;
                $user->email = $request->mail_lab;
            }
        }
        if ($request->has('status')) {
            $user->status = $request->status;
        }
        $user->save();

        $laboratorio->numsedes_lab = $request->numsedes_lab;
        $laboratorio->nit_lab          = $request->nit_lab;
        $laboratorio->nombre_lab       = $request->nombre_lab;
        $laboratorio->sigla_lab        = $request->sigla_lab;
        $laboratorio->id_nivel         = $request->id_nivel;
        $laboratorio->id_tipo          = $request->id_tipo;
        $laboratorio->id_categoria     = $request->id_categoria;
        $laboratorio->respo_lab        = $request->respo_lab;
        $laboratorio->ci_respo_lab     = $request->ci_respo_lab;
        $laboratorio->repreleg_lab     = $request->repreleg_lab;
        $laboratorio->ci_repreleg_lab  = $request->ci_repreleg_lab;
        $laboratorio->id_pais          = $request->id_pais;
        $laboratorio->id_dep           = $request->id_dep;
        $laboratorio->id_prov          = $request->id_prov;
        $laboratorio->id_municipio     = $request->id_municipio;
        $laboratorio->zona_lab         = $request->zona_lab;
        $laboratorio->direccion_lab    = $direccion;
        $laboratorio->wapp_lab         = $request->wapp_lab;
        $laboratorio->wapp2_lab        = $request->wapp2_lab;
        $laboratorio->mail2_lab        = $request->mail2_lab;
        $laboratorio->status           = $request->status ?? $user->status;
        $laboratorio->updated_by       = Auth::id();

        $laboratorio->save();
        $user = Auth::user();

        if ($user->isLaboratorio()) {
            return redirect()->route('lab.profile')->with('success', 'Tu informacion ha sido actualizado correctamente.');
        } else {
            return redirect()->route('laboratorio.index')->with('success', 'Laboratorio actualizado correctamente.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $laboratorio = Laboratorio::findOrFail($id);
        $user = $laboratorio->usuario;

        if ($laboratorio->inscripciones()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el laboratorio porque tiene inscripciones registradas.'
            ], 422);
        }
        $laboratorio->delete();

        if ($user) {
            $user->delete();
        }

        return redirect()->route('laboratorio.index')->with('success', 'Laboratorio y usuario eliminado correctamente.');
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

    public function toggleDescuento($id)
    {
        $lab = Laboratorio::find($id);
        if (!$lab) {
            return response()->json([
                'success' => false,
                'message' => 'Laboratorio no encontrado.'
            ], 404);
        }
        $lab->tiene_descuento = !$lab->tiene_descuento;
        $lab->save();

        return response()->json([
            'success' => true,
            'tiene_descuento' => $lab->tiene_descuento
        ]);
    }

    public function getData(Request $request)
    {
        if (!Gate::any([Permiso::GESTION_LABORATORIO, Permiso::ADMIN, Permiso::GESTION_INSCRIPCIONES])) {
            return redirect('/')->with('error', 'No tiene autorización para acceder a esta sección.');
        }
        $query = Laboratorio::query()->with(['pais', 'usuario', 'departamento', 'provincia', 'municipio', 'tipo', 'categoria', 'nivel']);

        foreach (['pais', 'dep', 'prov', 'municipio', 'tipo', 'categoria', 'nivel'] as $f) {
            if ($val = $request->get($f)) {
                $query->where("id_{$f}", $val);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('filter_descuento')) {
            $query->where('tiene_descuento', $request->filter_descuento);
        }

        return datatables()
            ->of($query)
            ->addColumn('pais_nombre', fn($lab) => $lab->pais->nombre_pais)
            ->addColumn('departamento_nombre', fn($lab) => $lab->departamento->nombre_dep ?? '-')
            ->addColumn('provincia_nombre', fn($lab) => $lab->provincia->nombre_prov ?? '-')
            ->addColumn('municipio_nombre', fn($lab) => $lab->municipio->nombre_municipio ?? '-')
            ->addColumn('codigo', fn($lab) => $lab->usuario->username)
            ->addColumn('tipo_nombre', fn($lab) => $lab->tipo->nombre_tipo ?? '-')
            ->addColumn('categoria_nombre', fn($lab) => $lab->categoria->nombre_categoria ?? '-')
            ->addColumn('nivel_nombre', fn($lab) => $lab->nivel->nombre ?? '-')
            ->addColumn('email', fn($lab) => $lab->usuario->email ?? '-')
            ->addColumn('status_label', fn($lab) => $lab->getStatusRaw())
            ->addColumn('actions', function ($lab) {
                return view('laboratorio.action-buttons', [
                    'showUrl' => route('laboratorio.show', $lab->id),
                    'editUrl' => route('laboratorio.edit', $lab->id),
                    'deleteUrl' => route('laboratorio.destroy', $lab->id),
                    'inscribirUrl' => route('inscripcion.create', $lab->id),
                    'nombre' => $lab->nombre_lab,
                    'id' => $lab->id,
                    'activo' => $lab->status,
                    'tiene_descuento' => $lab->tiene_descuento,
                ])->render();
            })
            ->rawColumns(['status_label', 'actions'])
            ->toJson();
    }

    public function getLabBySearch(Request $request)
    {

        if (!Gate::any([Permiso::GESTION_LABORATORIO, Permiso::ADMIN, Permiso::GESTION_INSCRIPCIONES])) {
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección.');
        }
        // $query = Laboratorio::query();
        $query = Laboratorio::orderBy('created_at', 'desc')->active();

        return datatables()
            ->of($query)
            ->addColumn('codigo', fn($lab) => $lab->cod_lab)
            ->addColumn('nombre_lab', fn($lab) => $lab->nombre_lab)
            ->addColumn('status_label', fn($lab) => $lab->getStatusRaw())
            ->addColumn('acciones', function ($lab) {
                $url = route('inscripcion.create', $lab->id);
                return '<a href="' . $url . '" 
                    target="_blank"
                    class="px-2 py-1 rounded-[5px] border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200 text-sm font-medium shadow-sm"
                >
                Inscribir
            </a>';
            })
            ->filterColumn('nombre_lab', function ($query, $keyword) {
                $query->whereRaw("LOWER(nombre_lab) LIKE ?", ["%" . strtolower($keyword) . "%"]);
            })
            ->rawColumns(['status_label', 'acciones'])
            ->toJson();
    }
}
