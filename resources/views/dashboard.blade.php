<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-700 flex items-center gap-2">
            <i class="fas fa-home text-indigo-500"></i> Escritorio
        </h2>
    </x-slot>

    <div class="bg-white p-6 rounded shadow">
        <p class="text-gray-800 text-sm leading-6">
            <i class="fas fa-check-circle text-green-500 mr-2"></i>
            Bienvenido al Sistema de Gestión del PEEC. <br>
            Su Usuario/Codigo es: <strong>{{ Auth::user()->username ?? '-/-' }}</strong>
        </p>
    </div>
</x-app-layout>
