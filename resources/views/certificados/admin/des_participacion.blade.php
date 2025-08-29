<x-app-layout>
    <div class="container py-1 max-w-4xl">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-xl font-bold text-gray-800">Certificados</h1>
        </div>

        <form id="form-gestion" method="POST" action="">
            @csrf
            <div>
                <label for="gestion" class="block text-sm font-medium text-gray-700">Gestión:</label>
                <select id="gestion" name="gestion"
                    class="mt-1 block w-40 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach ($gestiones as $gestion)
                        <option value="{{ $gestion }}">{{ $gestion }}</option>
                    @endforeach
                </select>
            </div>

            <div class="my-2 pt-5">
                <button type="submit" class="btn-primary"><i class="fas fa-save mr-1"></i>Publicar
                    certificados</button>
            </div>
        </form>

        <script>
            document.getElementById('form-gestion').addEventListener('submit', function(e) {
                e.preventDefault();
                let gestion = document.getElementById('gestion').value;

                let action = @json(route('certificados.publicar', ['gestion' => ':gestion']));

                this.action = action.replace(':gestion', gestion);

                this.submit();
            });
        </script>
    </div>
</x-app-layout>
