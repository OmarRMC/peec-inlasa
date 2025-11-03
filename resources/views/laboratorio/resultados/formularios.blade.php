<x-app-layout>
    <div class="container py-6 max-w-5xl">
        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
            <h1 class="text-xl font-bold text-primary">Formularios del Ensayo: {{ $ensayo->descripcion }}</h1>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre del Formulario</th>
                        <th>Llenar</th>
                        <th>Guía</th>
                        <th>Ver</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($formularios as $formulario)
                        <tr>
                            <td>{{ $formulario->codigo ?? '-' }}</td>
                            <td>{{ $formulario->nombre ?? '-' }}</td>
                            <td>
                                <a href="{{ route('lab.inscritos-ensayos.formularios.llenar', ['id'=>$formulario->id, 'idEA'=>$ensayo->id]) }}"
                                    class="bg-green-100 hover:bg-green-200 text-green-700 px-2 py-1 rounded shadow-sm"
                                    data-tippy-content="Llenar formulario">
                                    <i class="fas fa-pen"></i>
                                </a>
                            </td>
                            <td>
                                <a href="#"
                                    class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm"
                                    data-tippy-content="Ver guía">
                                    <i class="fas fa-book"></i>
                                </a>
                            </td>

                            <td>
                                <a href="#"
                                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded shadow-sm"
                                    data-tippy-content="Ver formulario">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-muted">
                                No hay formularios disponibles para este ensayo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
