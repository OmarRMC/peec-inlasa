  <x-app-layout>
      <div x-init="$nextTick(() => {
          tippy($el.querySelectorAll('[data-tippy-content]'), {
              theme: 'minimal',
              animation: 'fade',
              placement: 'top',
              allowHTML: true,
              maxWidth: 220,
              interactive: true,
          });
      
          $el.querySelectorAll('.campo-entrada').forEach(input => {
              if (input.dataset.validacionActiva) return;
              input.dataset.validacionActiva = true;
      
              let errorMsg = document.createElement('small');
              errorMsg.classList.add('text-red-600', 'text-xs', 'hidden');
              input.insertAdjacentElement('afterend', errorMsg);
              input.addEventListener('input', () => validarCampo(input, errorMsg));
              input.addEventListener('change', () => validarCampo(input, errorMsg));
          });
      
          function validarCampo(input, errorMsg) {
              console.log('uno');
              const pattern = new RegExp(input.getAttribute('pattern'));
              console.log(pattern.test(input.value))
              if (input.checkValidity()) {
                  console.log('dos');
                  errorMsg.textContent = '';
                  errorMsg.classList.add('hidden');
                  input.classList.remove('border-red-500');
                  input.classList.add('border-green-500');
              } else {
                  console.log('tres');
                  let mensaje = input.dataset.mensajeError || 'Valor inválido';
                  if (input.validity.valueMissing) mensaje = 'Este campo es obligatorio';
                  if (input.validity.tooShort) mensaje = `Mínimo ${input.minLength} caracteres`;
                  if (input.validity.tooLong) mensaje = `Máximo ${input.maxLength} caracteres`;
                  if (input.validity.rangeUnderflow) mensaje = `El valor debe ser >= ${input.min}`;
                  if (input.validity.rangeOverflow) mensaje = `El valor debe ser <= ${input.max}`;
                  if (input.validity.patternMismatch) {
                      mensaje = input.dataset.mensajeError || 'Formato inválido';
                  }
      
                  errorMsg.textContent = mensaje;
                  errorMsg.classList.remove('hidden');
                  input.classList.add('border-red-500');
                  input.classList.remove('border-green-500');
              }
          }
      })">
          {{-- Botón volver --}}
          <div class="mb-4">
              <button @click="cantidad = 0; actual = 0"
                  class="inline-flex items-center px-4 py-2 text-sm rounded-md transition"
                  style="background-color: {{ $primaryColor }}; color: #fff;">
                  <i class="fas fa-arrow-left mr-2"></i> Volver
              </button>
          </div>

          {{-- Información del formulario --}}
          <div class="grid grid-cols-2 gap-6 mb-4 text-center">
              <div class="p-3 rounded-lg shadow-sm" style="background-color: {{ $primaryColor }}20;">
                  <span class="block text-xs text-gray-500">Nombre del formulario</span>
                  <span class="text-sm font-semibold" style="color: {{ $primaryColor }}">
                      {{ $formulario->nombre }}
                  </span>
              </div>
              <div class="p-3 bg-gray-50 rounded-lg shadow-sm">
                  <span class="block text-xs text-gray-500">Código del formulario</span>
                  <span class="text-sm font-semibold text-gray-800">
                      {{ $formulario->codigo }}
                  </span>
              </div>
          </div>

          {{-- Nota --}}
          @if (!empty($formulario->nota))
              <div class="p-2 rounded-lg mb-2 bg-yellow-100 border border-yellow-300">
                  <span class="text-sm text-yellow-800">{{ $formulario->nota }}</span>
              </div>
          @endif

          {{-- Slides de formularios --}}
          <div class="relative" x-data="{ i: 1 }">
              {{-- <template x-for="i in cantidad" :key="i"> --}}
              <form action="{{ route('lab.resultados.store') }}" method="POST">
                  @csrf
                  <input type="text" hidden name="id_formulario" value="{{ $formulario->id }}">
                  @include('admin.formularios.partials.preview')
                  <div class="text-center">
                      <button type="submit" class="px-2 py-1 text-white rounded"
                          style="background-color: {{ $primaryColor }}">
                          Test de validación
                      </button>
                  </div>
              </form>
          </div>
      </div>
  </x-app-layout>
