@php use App\Models\Configuracion;  @endphp
<x-app-layout>

    <div class="max-w-5xl mx-auto px-4 py-6">
        <div class="bg-white border border-gray-200 shadow rounded-2xl p-6 space-y-6">
            <h1 class="text-xl font-bold text-primary mb-4">⚙️ Configuración del Certificados</h1>

            @if ($errors->any())
                <div class="bg-red-100 text-red-800 border border-red-200 p-4 mb-6 rounded-xl">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @foreach ([['id' => 'certificado', 'title' => 'Cetificado'], ['id' => 'gestionCertificado', 'title' => 'Gestión de registro de certificados']] as $item)
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <button type="button"
                        class="w-full flex justify-between items-center px-5 py-4 bg-indigo-50 hover:bg-indigo-100 font-semibold transition duration-300"
                        onclick="toggleCollapse('{{ $item['id'] }}')">
                        <span>{{ $item['title'] }}</span>
                        <svg id="icon-{{ $item['id'] }}" class="w-5 h-5 transform transition-transform duration-300"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="content-{{ $item['id'] }}"
                        class="max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
                        <div class="px-5 py-4 space-y-4 text-sm text-gray-700">
                            @php $config = 'configuracion.update'; @endphp

                            @switch($item['id'])
                                @case('certificado')
                                    <form action="{{ route($config, 'certificados') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf @method('PUT')

                                        <div class="flex flex-wrap gap-6">

                                            @foreach ($configuraciones as $config)
                                                <div class="border border-gray-200 rounded-xl p-4 flex-1 min-w-[300px]">
                                                    <h3 class="font-semibold text-sm mb-3">{{ $config->label ?? '' }}</h3>

                                                    <div class="space-y-3 text-sm">
                                                        <div>
                                                            <label class="block font-medium">Nombre</label>
                                                            <input type="text" name="{{ $config->clave }}[nombre]"
                                                                value="{{ old($config->clave . '.nombre', $config->nombre ?? '') }}"
                                                                pattern="^(?=.*[A-Za-zÀ-ÖØ-öø-ÿÑñ])[A-Za-zÀ-ÖØ-öø-ÿÑñ\s.]+$"
                                                                title="Solo letras, espacios y puntos; no puede ser solo espacios ni solo números."
                                                                required class="mt-1 input-standard">
                                                        </div>

                                                        <div>
                                                            <label class="block font-medium">Nombre del cargo</label>
                                                            <input type="text" name="{{ $config->clave }}[cargo]"
                                                                value="{{ old($config->clave . '.cargo', $config->cargo ?? '') }}"
                                                                pattern="^(?=.*[A-Za-zÀ-ÖØ-öø-ÿÑñ])[A-Za-zÀ-ÖØ-öø-ÿÑñ\s.[0-9]]+$"
                                                                title="Solo letras, espacios y puntos; no puede ser solo espacios ni solo números."
                                                                required class="mt-1 input-standard">
                                                        </div>
                                                        <div>
                                                            <label class="block font-medium">Imagen de firma</label>
                                                            <input type="file" name="{{ $config->clave }}[imagen]"
                                                                @if (!isset($config->imagen) || empty($config->imagen)) required @endif
                                                                accept="image/*" class="mt-1 input-standard">
                                                            @if (isset($config->imagen))
                                                                <div class="mt-2 text-center">
                                                                    <img src="{{ $config->imagen }}"
                                                                        alt="Firma  de {{ $config->nombre }}"
                                                                        class="h-16 object-contain mx-auto">
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="text-right mt-4">
                                            <button type="submit" class="btn-primary"><i
                                                    class="fas fa-save mr-1"></i>Guardar</button>
                                        </div>
                                    </form>
                                @break

                                @case('gestionCertificado')
                                    <form action="{{ route($config, 'gestionCertificado') }}" method="POST">
                                        @csrf @method('PUT')
                                        <div>
                                            <label for="{{ Configuracion::REGISTRO_PONDERACIONES_CERTIFICADOS_GESTION }}"
                                                class="block text-sm font-medium">Gestion de registro de Certificados</label>
                                            <input type="number"
                                                name="{{ Configuracion::REGISTRO_PONDERACIONES_CERTIFICADOS_GESTION }}"
                                                id="{{ Configuracion::REGISTRO_PONDERACIONES_CERTIFICADOS_GESTION }}"
                                                value="{{ old(Configuracion::REGISTRO_PONDERACIONES_CERTIFICADOS_GESTION, configuracion(Configuracion::REGISTRO_PONDERACIONES_CERTIFICADOS_GESTION) ?? date('Y')) }}"
                                                class="mt-1 input-standard" min="2020" max="2100" required>
                                        </div>
                                        <div class="text-right mt-4">
                                            <button type="submit" class="btn-primary"><i
                                                    class="fas fa-save mr-1"></i>Guardar</button>
                                        </div>
                                    </form>
                                @break
                            @endswitch
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        function toggleCollapse(id) {
            const content = document.getElementById('content-' + id);
            const icon = document.getElementById('icon-' + id);

            if (content.style.maxHeight && content.style.maxHeight !== '0px') {
                content.style.maxHeight = '0px';
                icon.classList.remove('rotate-180');
            } else {
                document.querySelectorAll('[id^="content-"]').forEach(el => el.style.maxHeight = '0px');
                document.querySelectorAll('[id^="icon-"]').forEach(el => el.classList.remove('rotate-180'));

                content.style.maxHeight = content.scrollHeight + 'px';
                icon.classList.add('rotate-180');
            }
        }
    </script>
</x-app-layout>
