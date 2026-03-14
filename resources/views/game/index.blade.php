<x-app-layout>
    <div class="container mx-auto py-6">

        @if (session('output'))
            <div class="bg-gray-100 p-4 rounded mb-4">
                <h2 class="font-semibold mb-2">Salida del comando:</h2>
                <pre>{{ session('output') }}</pre>

                @if (session('error'))
                    <h2 class="font-semibold mt-2 mb-2 text-red-600">Errores:</h2>
                    <pre class="text-red-600">{{ session('error') }}</pre>
                @endif
            </div>
        @endif

        <form method="POST" action="{{ route('game.run') }}">
            @csrf
            <div class="mb-4">
                <label class="block font-semibold mb-2" for="command">Selecciona un comando:</label>
                <select name="command" id="command" class="border p-2 w-full">
                    @foreach (config('game') as $key => $cmd)
                        <option value="{{ $key }}">{{ $key }} - {{ $cmd['description'] }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Ejecutar
            </button>
        </form>

        <hr class="my-6">

        <div>
            <h2 class="font-semibold mb-3">Descargar archivo o carpeta</h2>
            <form method="GET" action="{{ route('game.descargar.ruta') }}">
                <div class="mb-3">
                    <label class="block text-sm mb-1" for="path">Ruta relativa al proyecto (ej: <code>app/Http/Controllers</code> o <code>config/game.php</code>):</label>
                    <input type="text" name="path" id="path" class="border p-2 w-full rounded" placeholder="app/Http/Controllers" required>
                </div>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Descargar
                </button>
            </form>
            <p class="text-xs text-gray-500 mt-2">Si es una carpeta, se descarga como ZIP. Si es un archivo, se descarga directamente.</p>
        </div>
    </div>
</x-app-layout>
