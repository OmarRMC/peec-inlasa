<x-app-layout>
    <div class="p-6 max-w-2xl mx-auto">
        {{-- Encabezado con botón volver --}}
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">
                {{ isset($cargo) ? 'Editar Cargo' : 'Crear Cargo' }}
            </h1>
            <a href="{{ route('cargos.index') }}"
                class="inline-flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 transition">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        {{-- Mostrar errores generales --}}
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-md text-sm">
                <strong class="block font-medium mb-1">Ups, algo salió mal:</strong>
                <ul class="list-disc ml-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Formulario --}}
        <form action="{{ $action }}" method="POST" class="space-y-6">
            @csrf
            @if (isset($cargo))
                @method('PUT')
            @endif

            {{-- Nombre del cargo --}}
            <div>
                <label for="nombre_cargo" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Cargo</label>
                <input type="text" name="nombre_cargo" id="nombre_cargo"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm 
                    focus:border-indigo-400 focus:ring-1 focus:ring-indigo-300 focus:outline-none transition 
                    @error('nombre_cargo') border-red-400 @enderror"
                    value="{{ old('nombre_cargo', $cargo->nombre_cargo ?? '') }}" placeholder="Nombre del Cargo">

                @error('nombre_cargo')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Descripción --}}
            <div>
                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <input type="text" name="descripcion" id="descripcion"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm 
                    focus:border-indigo-400 focus:ring-1 focus:ring-indigo-300 focus:outline-none transition 
                    @error('descripcion') border-red-400 @enderror"
                    value="{{ old('descripcion', $cargo->descripcion ?? '') }}" placeholder="Descripción del cargo">

                @error('descripcion')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Observaciones --}}
            <div>
                <label for="obs" class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                <textarea name="obs" id="obs" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm 
                    focus:border-indigo-400 focus:ring-1 focus:ring-indigo-300 focus:outline-none transition 
                    @error('obs') border-red-400 @enderror"
                    placeholder="Observaciones adicionales">{{ old('obs', $cargo->obs ?? '') }}</textarea>

                @error('obs')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Estado --}}
            <div class="flex items-center gap-2">
                {{-- Este input oculto garantiza que se envíe "0" si el checkbox no está marcado --}}
                <input type="hidden" name="status" value="0">

                <input type="checkbox" name="status" id="status" value="1"
                    class="text-indigo-600 focus:ring-indigo-300 focus:ring-1 rounded 
                    @error('status') border-red-400 @enderror"
                    {{ old('status', $cargo->status ?? true) ? 'checked' : '' }}>

                <label for="status" class="text-sm text-gray-700">Activo</label>
            </div>
            
            {{-- Botones --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('cargos.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-100 transition">
                    <i class="fas fa-times"></i> Cancelar
                </a>

                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-500 transition">
                    <i class="fas fa-save"></i> {{ isset($cargo) ? 'Actualizar' : 'Crear' }}
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
