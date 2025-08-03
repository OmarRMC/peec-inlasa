<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Cargo;
use App\Models\EnsayoAptitud;
use App\Models\Permiso;
use App\Models\User;
use App\Notifications\VerificarCorreoUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('canany:' . Permiso::ADMIN . ',' . Permiso::GESTION_USUARIO)->only(['index', 'create', 'update', 'destroy', 'show', 'edit']);
    }
    public function index()
    {
        $usuarios = User::with(['cargo', 'permisos'])
            ->whereDoesntHave('laboratorio')
            ->get();
        return view('usuario.index', compact('usuarios'));
    }

    public function create()
    {
        $cargos = Cargo::active()->get();
        $permisos = Permiso::active()->listar()
            ->get();
        $areas = Area::active()->active()->orderBy('descripcion')->get();
        $ensayoA = EnsayoAptitud::active()->orderBy('descripcion')->get();
        return view('usuario.create', compact('cargos', 'permisos', 'ensayoA', 'areas'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'username'     => 'required|string|max:50|unique:users,username',
            'nombre'       => 'required|string|max:50',
            'ap_paterno'   => 'required|string|max:50',
            'ap_materno'   => 'nullable|string|max:50',
            'ci'           => 'required|string|max:15|unique:users,ci',
            'email'        => 'required|email|unique:users,email',
            'telefono'     => 'nullable|string|max:20',
            'password'     => 'required|string|min:8|confirmed',
            'id_cargo'     => 'nullable|exists:cargo,id',
            'status'       => 'required|boolean',
            'permisos'     => 'array',
            'permisos.*'   => 'exists:permiso,id',
            'ensayos_ap'   => 'nullable|array',
            'ensayos_ap.*' => 'exists:ensayo_aptitud,id',
        ], $this->messages());

        $usuario = User::create([
            'username'   => $request->username,
            'nombre'     => $request->nombre,
            'ap_paterno' => $request->ap_paterno,
            'ap_materno' => $request->ap_materno,
            'ci'         => $request->ci,
            'email'      => $request->email,
            'telefono'   => $request->telefono,
            'password'   => $request->password,
            'id_cargo'   => $request->id_cargo,
            'status'     => $request->status,
        ]);

        $usuario->notify(new VerificarCorreoUser($usuario));
        $usuario->permisos()->sync($request->input('permisos', []));

        $ensayoIDs = $request->input('ensayos_ap', []);

        // $ensayosData = [];
        // $ensayos = EnsayoAptitud::whereIn('id', $ensayoIDs)->get();

        // foreach ($ensayos as $ensayo) {
        //     $ensayosData[$ensayo->id] = ['descripcion' => $ensayo->descripcion];
        // }

        $usuario->responsablesEA()->attach($ensayoIDs);

        return redirect()->route('usuario.index')->with('success', 'Usuario creado correctamente.');
    }

    public function show(User $usuario)
    {
        $usuario->load([
            'cargo',
            'permisos',
            'responsablesEA',
            'responsablesEA.paquete'
        ]);
        $responsablesEA = $usuario->responsablesEA;
        return view('usuario.show', compact('usuario', 'responsablesEA'));
    }

    public function edit(User $usuario)
    {
        $usuario->load([
            'cargo',
            'permisos',
            'responsablesEA',
            'responsablesEA.paquete'
        ]);
        $cargos = Cargo::active()->get();
        $permisos = Permiso::active()->listar()->get();
        $ensayoA = EnsayoAptitud::active()->get();
        $areas = Area::active()->active()->orderBy('descripcion')->get();
        $responsablesEA = $usuario->responsablesEA->mapWithKeys(function ($ensayo) {
            $paquete = $ensayo->paquete;
            $descPaquete = mb_strtolower(trim($paquete->descripcion), 'UTF-8');
            $descEnsayo = mb_strtolower(trim($ensayo->descripcion), 'UTF-8');

            $descripcion = $descPaquete === $descEnsayo
                ? $ensayo->descripcion
                : "$paquete->descripcion - $ensayo->descripcion";

            return [$ensayo->id => $descripcion];
        });
        $ensayosSeleccionados = old('ensayos_ap', $responsablesEA);
        return view('usuario.edit', compact('usuario', 'cargos', 'permisos', 'ensayoA', 'ensayosSeleccionados', 'areas'));
    }

    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'username'     => 'required|string|max:50|unique:users,username,' . $usuario->id,
            'nombre'       => 'required|string|max:50',
            'ap_paterno'   => 'required|string|max:50',
            'ap_materno'   => 'nullable|string|max:50',
            'ci'           => 'required|string|max:15|unique:users,ci,' . $usuario->id,
            'email'        => 'required|email|unique:users,email,' . $usuario->id,
            'telefono'     => 'nullable|string|max:20',
            'password'     => 'nullable|string|min:8|confirmed',
            'id_cargo'     => 'nullable|exists:cargo,id',
            'status'       => 'required|boolean',
            'ensayos_ap'   => 'nullable|array',
            'ensayos_ap.*' => 'exists:ensayo_aptitud,id',
        ], $this->messages());

        $usuario->update([
            'username'   => $request->username,
            'nombre'     => $request->nombre,
            'ap_paterno' => $request->ap_paterno,
            'ap_materno' => $request->ap_materno,
            'ci'         => $request->ci,
            'email'      => $request->email,
            'telefono'   => $request->telefono,
            'id_cargo'   => $request->id_cargo,
            'status'     => $request->status,
        ]);

        if (!empty($validated['password'])) {
            $usuario->update(['password' => Hash::make($validated['password'])]);
        }
        $ensayoIDs = $request->input('ensayos_ap', []);
        $usuario->permisos()->sync($request->input('permisos', []));

        // $ensayosData = [];
        // $ensayos = EnsayoAptitud::whereIn('id', $ensayoIDs)->get();

        // foreach ($ensayos as $ensayo) {
        //     $ensayosData[$ensayo->id] = ['descripcion' => $ensayo->descripcion];
        // }

        $usuario->responsablesEA()->sync($ensayoIDs);
        return redirect()->route('usuario.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {

        $usuario->delete();
        // if ($user && $user->laboratorios()->count() <= 1) {
        //     $user->delete();
        // }
        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado correctamente.'
        ]);
    }

    private function messages()
    {
        return [
            'username.required'     => 'El nombre de usuario es obligatorio.',
            'username.unique'       => 'Este nombre de usuario ya está en uso.',
            'username.max'          => 'El nombre de usuario no debe exceder los 50 caracteres.',

            'nombre.required'       => 'El nombre es obligatorio.',
            'nombre.max'            => 'El nombre no debe exceder los 50 caracteres.',

            'ap_paterno.required'   => 'El apellido paterno es obligatorio.',
            'ap_paterno.max'        => 'El apellido paterno no debe exceder los 50 caracteres.',

            'ap_materno.max'        => 'El apellido materno no debe exceder los 50 caracteres.',

            'ci.required'           => 'El número de CI es obligatorio.',
            'ci.unique'             => 'Este número de CI ya está registrado.',
            'ci.max'                => 'El número de CI no debe exceder los 15 caracteres.',

            'email.required'        => 'El correo electrónico es obligatorio.',
            'email.email'           => 'Debe ser un correo electrónico válido.',
            'email.unique'          => 'Este correo ya está en uso.',

            'telefono.max'          => 'El número de teléfono no debe exceder los 20 caracteres.',

            'password.required'     => 'La contraseña es obligatoria.',
            'password.min'          => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'    => 'La confirmación de la contraseña no coincide.',

            'id_cargo.exists'       => 'El cargo seleccionado no es válido.',

            'status.required'       => 'El estado es obligatorio.',
            'status.boolean'        => 'El estado debe ser verdadero o falso.',
        ];
    }


    public function getData()
    {
        $query = User::with(['cargo', 'permisos'])
            ->whereDoesntHave('laboratorio')
            ->select(['id', 'created_at', 'username', 'nombre', 'ap_paterno', 'ap_materno', 'id_cargo', 'status']);

        // $query = User::with('cargo')
        //     ->select(['id', 'username', 'nombre', 'ap_paterno', 'ap_materno', 'id_cargo', 'status']);

        return DataTables::of($query)
            ->addColumn('nombre_completo', fn($u) => "{$u->nombre} {$u->ap_paterno} {$u->ap_materno}")
            ->addColumn('cargo', fn($u) => optional($u->cargo)->nombre_cargo ?? '<em>Sin cargo</em>')
            ->addColumn(
                'status_label',
                fn($u) =>
                $u->status
                    ? '<span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-green-500 text-white">Activo</span>'
                    : '<span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-red-500 text-white">Inactivo</span>'
            )
            ->addColumn('actions', function ($u) {
                return view('usuario.action-buttons', [
                    'id' => $u->id,
                    'nombre' => $u->username,
                    'editUrl' => route('usuario.edit', $u->id),
                    'showUrl' => route('usuario.show', $u->id),
                    'deleteUrl' => route('usuario.destroy', $u->id)
                ])->render();
            })
            ->rawColumns(['status_label', 'actions', 'cargo'])
            ->make(true);
    }
}
