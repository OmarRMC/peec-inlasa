<x-guest-layout>
    <div class="max-w-md mx-auto mt-10 bg-white border border-gray-200 shadow-md rounded-xl p-6 space-y-6">

        <h1 class="text-2xl font-bold text-indigo-700">Verificación de Correo</h1>

        <p class="text-sm text-gray-700 leading-relaxed">
            Gracias por registrarte. Antes de comenzar, por favor verifica tu dirección de correo electrónico haciendo
            clic en el enlace que te enviamos. <br><br>
            Si no recibiste el correo, puedes solicitar uno nuevo.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="text-sm font-medium text-green-600 bg-green-50 border border-green-200 rounded-lg p-3">
                Se ha enviado un nuevo enlace de verificación al correo electrónico que proporcionaste.
            </div>
        @endif

        <div class="flex items-center justify-between gap-4 pt-4 border-t border-gray-200">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn-primary">
                    <i class="fas fa-paper-plane mr-1"></i> Reenviar correo
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="text-sm text-gray-500 hover:text-red-600 transition underline focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
