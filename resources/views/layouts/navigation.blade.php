@php
    $authUser = Auth::user();
@endphp
<nav class="flex-1 px-2 py-4 space-y-1 text-sm overflow-y-auto">
    @if ($authUser?->isLaboratorio())
        @include('layouts.navigation._laboratorio')
    @else
        @include('layouts.navigation._staff')
    @endif
</nav>
