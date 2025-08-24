{{-- resources/views/verificacion/certificado/lab.blade.php --}}
@php
    use App\Models\Certificado;
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Validación del certificado</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100">
    <div class="mx-auto max-w-6xl px-4 py-10">
        <div class="rounded-2xl bg-white shadow-xl ring-1 ring-slate-200">
            {{-- Encabezado --}}
            <div class="border-b border-slate-200 p-8 text-center">
                <div
                    class="mx-auto mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 ring-1 ring-emerald-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                            d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </div>
                <h1 class="text-2xl font-semibold text-slate-800">El código de certificado es válido</h1>
                {{-- <p class="mt-1 text-sm text-slate-500">Verificación en línea del Programa de Evaluación Externa de la
                    Calidad
                </p> --}}
            </div>

            <div class="grid gap-4 px-6 py-2 md:grid-cols-4 mt-4">
                <div class="rounded-[5px] bg-slate-50 px-4 py-2 ring-1 ring-slate-200">
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Laboratorio</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $laboratorio->nombre_lab ?? '—' }}
                    </dd>
                </div>
                <div class="rounded-[5px] bg-slate-50 px-4 py-2 ring-1 ring-slate-200">
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Código</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $laboratorio->cod_lab ?? '—' }}
                    </dd>
                </div>
                <div class="rounded-[5px] bg-slate-50 px-4 py-2 ring-1 ring-slate-200">
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Tipo de Certificado</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-800">
                        {{ Certificado::NAME_TYPE_CERTIFICADO[$type] ?? 'N/A' }}
                    </dd>
                </div>
                <div class="rounded-[5px] bg-slate-50 px-4 py-2 ring-1 ring-slate-200">
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Gestión</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $gestion ?? '—' }}
                    </dd>
                </div>
            </div>

            {{-- Contenido según tipo --}}
            <div class="px-6 pb-8">

                @if ((int) ($type ?? 0) === Certificado::TYPE_DESEMP)
                    {{-- Tabla de DESEMPEÑO: $data = [ 'Área' => ['certificado'=>..., 'detalles'=>[['ensayo'=>..., 'ponderacion'=>...], ...]], ... ] --}}
                    <div class="overflow-x-auto rounded-[5px] ring-1 ring-slate-200">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                        Área</th>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                        Ensayo</th>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                        Calificación de desempeño</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse ($data as $area => $grupo)
                                    @php $rows = $grupo['detalles'] ?? []; @endphp
                                    @foreach ($rows as $idx => $row)
                                        <tr class="hover:bg-slate-50/60">
                                            @if ($idx === 0)
                                                <td class="whitespace-nowrap align-top px-4 py-2 text-sm font-semibold text-slate-800"
                                                    rowspan="{{ max(1, count($rows)) }}">
                                                    {{ $area }}
                                                </td>
                                            @endif
                                            <td class="px-4 py-2 text-sm text-slate-700">
                                                {{ $row['ensayo'] ?? '—' }}
                                            </td>
                                            <td class="px-4 py-2 text-sm">
                                                {{-- Píldora visual para la ponderación --}}
                                                <span
                                                    class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">
                                                    {{ $row['ponderacion'] ?? '—' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-6 text-center text-sm text-slate-500">Sin
                                            registros de desempeño para la gestión indicada.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @elseif ((int) ($type ?? 0) === Certificado::TYPE_PARTICIPACION)
                    {{-- PARTICIPACIÓN: $data = "ensayo1, ensayo2, ..." --}}
                    @php
                        $chips = collect(explode(',', (string) $data))->map(fn($t) => trim($t))->filter();
                    @endphp

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
                        <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-600">
                            Participación en:
                        </h2>
                        @if ($chips->isEmpty())
                            <p class="text-sm text-slate-500">No se encontraron ensayos de participación para la gestión
                                indicada.</p>
                        @else
                            <div class="flex flex-wrap gap-2">
                                @foreach ($chips as $chip)
                                    <span
                                        class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-200">
                                        {{ $chip }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <div class="rounded-[5px] border border-amber-200 bg-amber-50 p-4 text-amber-800">
                        No se pudo determinar el tipo de certificado.
                    </div>
                @endif
            </div>

            {{-- Footer con botón --}}
            <div class="flex items-center justify-center gap-3 border-t border-slate-200 bg-slate-50 p-6">
                <a href="{{ url('/') }}"
                    class="inline-flex items-center justify-center rounded-[5px] px-5 py-2.5 text-sm font-semibold text-white shadow-sm ring-1 ring-blue-600/10 bg-blue-600 hover:bg-blue-700">
                    Regresar al sistema
                </a>
                {{-- <button type="button" onclick="history.back()"
                    class="inline-flex items-center justify-center rounded-xl px-5 py-2.5 text-sm font-semibold text-blue-700 bg-white shadow-sm ring-1 ring-blue-200 hover:bg-blue-50">
                    Volver
                </button> --}}
            </div>
        </div>

        {{-- Pie institucional --}}
        <div class="mt-8 text-center text-xs text-slate-500">
            © 2025 | Instituto Nacional de Laboratorios de Salud · Programa de Evaluación Externa de la Calidad
        </div>
    </div>
</body>

</html>
