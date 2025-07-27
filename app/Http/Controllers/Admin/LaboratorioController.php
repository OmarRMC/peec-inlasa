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
        $this->middleware('canany:' . Permiso::GESTION_LABORATORIO . ',' . Permiso::ADMIN)->only(['index', 'create', 'store', 'show', 'edit', 'destroy', 'getData']);
    }

    public function index()
    {
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
            'cod_lab' => 'nullable|string|max:20|unique:laboratorio,cod_lab',
            'antcod_peec' => 'nullable|string|max:10',
            'numsedes_lab' => 'nullable|string|max:15',
            'nombre_lab' => 'required|string|max:100',
            'sigla_lab' => 'nullable|string|max:20|unique:laboratorio,sigla_lab',
            'nit_lab' => 'required|numeric|unique:laboratorio,nit_lab',
            'id_nivel' => 'required|exists:nivel_laboratorio,id',
            'id_tipo' => 'required|exists:tipo_laboratorio,id',
            'id_categoria' => 'required|exists:categoria,id',
            'respo_lab' => 'required|string|max:50',
            'ci_respo_lab' => 'required|string|max:25',
            'repreleg_lab' => 'required|string|max:50',
            'ci_repreleg_lab' => 'required|string|max:25',
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

        // Obtener sigla del país
        $pais = Pais::find($request->id_pais);
        $sigla = strtoupper($pais->sigla_pais); // Ej: BOL

        // Generar username incremental
        $count = User::where('username', 'LIKE', "$sigla%")->count() + 1;
        $username = $sigla . str_pad($count, 4, '0', STR_PAD_LEFT);

        // Crear usuario
        try {
            DB::beginTransaction();
            $user = User::create([
                'nombre' => $request->nombre_lab,
                'username' => $username,
                'email' => $request->mail_lab,
                'password' => $request->password,
                'status' => false,
                'ap_paterno' => 'Laboratorio_ap_paterno', // Asignar un apellido por defecto
                'ci' => $request->ci_respo_lab ?: '00000000',
                'telefono' => $request->telefono,
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
                'direccion_lab' => $request->direccion_lab,
                'wapp_lab' => $request->wapp_lab,
                'wapp2_lab' => $request->wapp2_lab,
                'mail_lab' => $request->mail_lab,
                'mail2_lab' => $request->mail2_lab,
                'status' => false,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
            DB::commit();
            try {
                $user->notify(new VerificarCorreoLab($user, $lab));
            } catch (\Throwable $th) {
                return redirect()->route('laboratorio.index')->with('warning', 'Laboratorio registrado correctamente, pero no se pudo enviar el correo de verificación.');
            }
            return redirect()->route('laboratorio.index')->with('success', 'Laboratorio registrado correctamente. Se envió un correo de verificación.');
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
            'niveles' => NivelLaboratorio::all(),
            'tipos' => TipoLaboratorio::all(),
            'departamentos' => $laboratorio->pais->departamentos()->orderBy('nombre_dep')->get(),
            'provincias' => $laboratorio->departamento->provincias()->orderBy('nombre_prov')->get(),
            'municipios' => $laboratorio->provincia->municipios()->orderBy('nombre_municipio')->get(),
            'categorias' => CategoriaLaboratorio::all(),
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
            'sigla_lab' => 'nullable|string|max:20|unique:laboratorio,sigla_lab,' . $laboratorio->id,
            'nit_lab' => 'required|numeric|unique:laboratorio,nit_lab,' . $laboratorio->id,
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
            'mail_lab' => 'required|email|unique:users,email,' . $user->id,
            'mail2_lab' => 'nullable|email',
            'telefono' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'nullable|boolean',
        ], $this->messages());
        $user->nombre = $request->nombre_lab;
        $user->telefono = $request->telefono;
        $user->ci = $request->ci_respo_lab ?: '00000000';

        if ($request->password) {
            $user->password = $request->password;
        }

        if ($request->email_verified_at && !$user->email_verified_at) {
            $user->email_verified_at = now();
        }

        if (Gate::any(abilities: [Permiso::GESTION_LABORATORIO, Permiso::ADMIN])) {
            if ($request->mail_lab != $laboratorio->mail_lab) {
                $laboratorio->mail_lab = $request->mail_lab;
                $user->email_verified_at = null;
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
        $laboratorio->direccion_lab    = $request->direccion_lab;
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

    public function getData(Request $request)
    {
        $query = Laboratorio::query()->with(['pais', 'usuario', 'departamento', 'provincia', 'municipio', 'tipo', 'categoria', 'nivel']);

        foreach (['pais', 'dep', 'prov', 'mun', 'tipo', 'categoria', 'nivel'] as $f) {
            if ($val = $request->get($f)) {
                $query->where("id_{$f}", $val);
            }
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
            ->addColumn('status_label', fn($lab) => $lab->status ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>')
            ->addColumn('actions', function ($lab) {
                return view('laboratorio.action-buttons', [
                    'showUrl' => route('laboratorio.show', $lab->id),
                    'editUrl' => route('laboratorio.edit', $lab->id),
                    'deleteUrl' => route('laboratorio.destroy', $lab->id),
                    'inscribirUrl' => route('inscripcion.create', $lab->id),
                    'nombre' => $lab->nombre_lab,
                    'id' => $lab->id,
                    'activo' => $lab->status, // ← Aquí pasamos si está activo o no
                ])->render();
            })
            ->rawColumns(['status_label', 'actions'])
            ->toJson();
    }
}
