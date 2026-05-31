@props([
    'icon' => 'fa-circle',
    'label',
    'routes' => [],
])
@php
    $menuId = 'nm_' . uniqid();
    $isActive = !empty($routes) && request()->routeIs(...(array) $routes);
@endphp
<div>
    <button
        x-data="{ open: {{ $isActive ? 'true' : 'false' }} }"
        x-init="if (open) openMenu = '{{ $menuId }}'"
        @click="openMenu !== '{{ $menuId }}' ? openMenu = '{{ $menuId }}' : openMenu = null"
        class="w-full flex items-center gap-3 px-2 py-2 rounded text-left transition
            {{ $isActive ? 'bg-indigo-50' : 'hover:bg-indigo-50' }}">
        <i class="fas {{ $icon }} w-5 {{ $isActive ? 'text-indigo-600' : 'text-indigo-500' }}"></i>
        <span>{{ $label }}</span>
        <i class="fas ml-auto text-xs text-gray-400"
            :class="openMenu === '{{ $menuId }}' ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
    </button>
    <div x-show="openMenu === '{{ $menuId }}'" x-collapse.duration.200ms
        class="ml-4 mt-1 space-y-1"
        @if($isActive) style="display: block;" @else style="display: none;" @endif>
        {{ $slot }}
    </div>
</div>
