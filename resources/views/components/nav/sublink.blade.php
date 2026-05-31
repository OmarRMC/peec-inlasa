@props([
    'href' => '#',
    'icon' => 'fa-circle',
    'label',
    'target' => null,
    'route' => null,
])
@php
    $isActive = $route && request()->routeIs(...(array) $route);
@endphp
<a href="{{ $href }}" @if($target) target="{{ $target }}" @endif
    class="block px-3 py-1 text-sm rounded flex items-center gap-2 transition border-l-2 text-gray-600
        {{ $isActive ? 'bg-indigo-100 border-indigo-600' : 'hover:bg-indigo-100 border-transparent' }}">
    <i class="fas {{ $icon }} w-4 {{ $isActive ? 'text-indigo-600' : 'text-indigo-500' }}"></i>
    <span>{{ $label }}</span>
</a>
