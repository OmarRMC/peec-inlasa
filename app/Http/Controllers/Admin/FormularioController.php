<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formulario;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FormularioController extends Controller
{
    /**
     * Mostrar todos los formularios.
     */
    public function index()
    {
        $formularios = Formulario::latest()->paginate(10);
        return view('formularios.index', compact('formularios'));
    }

    /**
     * Mostrar el formulario de creación.
     */
    public function create()
    {
        return view('formularios.create');
    }

    /**
     * Almacenar un nuevo formulario.
     */
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:25|unique:formulario,codigo',
            'proceso' => 'required|string|max:50|unique:formulario,proceso',
            'titulo' => 'nullable|string|max:255',
            'fec_formulario' => 'nullable|date',
            'version' => 'nullable|string',
            'status' => 'required|boolean',
        ], $this->messages());

        Formulario::create($request->all());

        return redirect()->route('formularios.index')->with('success', 'Formulario creado correctamente.');
    }

    /**
     * Mostrar un formulario específico.
     */
    public function show(Formulario $formulario)
    {
        return view('formularios.show', compact('formulario'));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(Formulario $formulario)
    {
        return view('formularios.edit', compact('formulario'));
    }

    /**
     * Actualizar un formulario.
     */
    public function update(Request $request, Formulario $formulario)
    {
        $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:25',
                Rule::unique('formulario', 'codigo')->ignore($formulario->id),
            ],
            'proceso' => [
                'required',
                'string',
                'max:50',
                Rule::unique('formulario', 'proceso')->ignore($formulario->id),
            ],
            'titulo' => 'required|string|max:255',
            'fec_formulario' => 'nullable|date',
            'version' => 'nullable|string',
            'status' => 'required|boolean',
        ], $this->messages());

        $formulario->update($request->all());

        return redirect()->route('formularios.index')->with('success', 'Formulario actualizado correctamente.');
    }

    /**
     * Eliminar un formulario.
     */
    public function destroy(Formulario $formulario)
    {
        $formulario->delete();

        return redirect()->route('formularios.index')->with('success', 'Formulario eliminado correctamente.');
    }

    /**
     * Mensajes personalizados de validación.
     */
    protected function messages()
    {
        return [
            'codigo.required' => 'El campo código es obligatorio.',
            'codigo.unique' => 'El código ya está registrado.',
            'codigo.max' => 'El código no debe exceder los 25 caracteres.',

            'proceso.required' => 'El campo proceso es obligatorio.',
            'proceso.unique' => 'El proceso ya está registrado.',
            'proceso.max' => 'El proceso no debe exceder los 50 caracteres.',

            'titulo.required' => 'El campo título es obligatorio.',
            'titulo.max' => 'El título no debe exceder los 255 caracteres.',

            'fec_formulario.date' => 'La fecha del formulario no tiene un formato válido.',

            'status.required' => 'El campo estado es obligatorio.',
            'status.boolean' => 'El estado debe ser válido (activo o inactivo).',
        ];
    }
}
