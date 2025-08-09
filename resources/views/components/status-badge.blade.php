@props(['value'])

@php
    $color = match (Str::lower($value)) {
        'en revision' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
        'aprobado' => 'bg-green-100 text-green-800 border-green-300',
        'vencido' => 'bg-red-100 text-red-800 border-red-300',
        'pagado' => 'bg-green-100 text-green-800 border-green-300',
        'deudor' => 'bg-red-100 text-red-800 border-red-300',
        '1' => 'bg-green-100 text-green-800 border-green-300',
        '0' => 'bg-red-100 text-red-800 border-red-300',
        default => 'bg-gray-100 text-gray-800 border-gray-300',
    };
@endphp

<span class="inline-block text-xs font-medium px-2 py-1 border rounded-full {{ $color }}">
    @switch($value)
        @case('1')
            Activo
        @break

        @case('0')
            Inactivo
        @break

        @default
            {{ $value }}
    @endswitch

</span>
