@props([
    'href' => '#',
    'icon' => 'fa-circle',
    'label',
    'badge' => null,
    'target' => null,
    'route' => null,
])
@php
    $isActive = $route && request()->routeIs(...(array) $route);
@endphp
<a href="{{ $href }}" @if($target) target="{{ $target }}" @endif
    class="w-full flex items-center gap-3 px-2 py-2 rounded relative transition border-l-2
        {{ $isActive ? 'bg-indigo-100 border-indigo-600' : 'hover:bg-indigo-50 border-transparent' }}">
    <i class="fas {{ $icon }} w-5 {{ $isActive ? 'text-indigo-600' : 'text-indigo-500' }}"></i>
    <span>{{ $label }}</span>
    @if (!is_null($badge) && $badge > 0)
        <span class="ml-auto inline-flex items-center justify-center text-xs font-bold text-white bg-red-600 rounded-full h-5 min-w-[1.25rem] px-1">
            {{ $badge }}
        </span>
    @endif
</a>
