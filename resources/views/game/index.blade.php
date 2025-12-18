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
    </div>
</x-app-layout>
