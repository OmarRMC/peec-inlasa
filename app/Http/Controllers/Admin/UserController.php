<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use App\Models\Permiso;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
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
        $permisos = Permiso::active()->get();
        return view('usuario.create', compact('cargos', 'permisos'));
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
            'talefono'     => 'nullable|string|max:20',
            'password'     => 'required|string|min:8|confirmed',
            'id_cargo'     => 'nullable|exists:cargo,id',
            'status'       => 'required|boolean',
            'permisos'     => 'array',
            'permisos.*'   => 'exists:permiso,id',
        ], $this->messages());

        $usuario = User::create([
            'username'   => $request->username,
            'nombre'     => $request->nombre,
            'ap_paterno' => $request->ap_paterno,
            'ap_materno' => $request->ap_materno,
            'ci'         => $request->ci,
            'email'      => $request->email,
            'talefono'   => $request->talefono,
            'password'   => $request->password,
            'id_cargo'   => $request->id_cargo,
            'status'     => $request->status,
        ]);

        $usuario->permisos()->sync($request->input('permisos', []));
        return redirect()->route('usuario.index')->with('success', 'Usuario creado correctamente.');
    }

    public function show(User $usuario)
    {
        return view('usuario.show', compact('usuario'));
    }

    public function edit(User $usuario)
    {
        $cargos = Cargo::active()->get();
        $permisos = Permiso::active()->get();
        return view('usuario.edit', compact('usuario', 'cargos', 'permisos'));
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
            'talefono'     => 'nullable|string|max:20',
            'password'     => 'nullable|string|min:8|confirmed',
            'id_cargo'     => 'nullable|exists:cargo,id',
            'status'       => 'required|boolean',
        ], $this->messages());

        $usuario->update([
            'username'   => $request->username,
            'nombre'     => $request->nombre,
            'ap_paterno' => $request->ap_paterno,
            'ap_materno' => $request->ap_materno,
            'ci'         => $request->ci,
            'email'      => $request->email,
            'talefono'   => $request->talefono,
            'id_cargo'   => $request->id_cargo,
            'status'     => $request->status,
        ]);

        if (!empty($validated['password'])) {
            $usuario->update(['password' => Hash::make($validated['password'])]);
        }

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

            'talefono.max'          => 'El número de teléfono no debe exceder los 20 caracteres.',

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
            ->select(['id','created_at', 'username', 'nombre', 'ap_paterno', 'ap_materno', 'id_cargo', 'status']);

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
