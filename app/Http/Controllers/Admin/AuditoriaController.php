<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permiso;
use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;
use Yajra\DataTables\Facades\DataTables;

class AuditoriaController extends Controller
{
    /**
     * Mapa de tipos auditables (clase del modelo) a una etiqueta legible en español.
     */
    public const ENTIDADES = [
        \App\Models\Inscripcion::class        => 'Inscripción',
        \App\Models\DetalleInscripcion::class => 'Detalle de inscripción',
        \App\Models\DocumentoInscripcion::class => 'Documento de inscripción',
        \App\Models\InscripcionEA::class      => 'Inscripción a ensayo',
        \App\Models\VigenciaInscripcion::class => 'Vigencia de inscripción',
        \App\Models\Pago::class               => 'Pago',
        \App\Models\Laboratorio::class        => 'Laboratorio',
        \App\Models\Certificado::class        => 'Certificado',
        \App\Models\DetalleCertificado::class => 'Detalle de certificado',
        \App\Models\InformeTecnico::class     => 'Informe técnico',
        \App\Models\Notificacion::class       => 'Notificación',
        \App\Models\NotificacionLab::class    => 'Notificación a laboratorio',
        \App\Models\Contrato::class           => 'Contrato',
        \App\Models\ContratoDetalle::class    => 'Detalle de contrato',
        \App\Models\User::class               => 'Usuario',
        \App\Models\Permiso::class            => 'Permiso',
    ];

    /**
     * Mapa de eventos a una etiqueta legible en español.
     */
    public const EVENTOS = [
        'created'   => 'Creación',
        'updated'   => 'Modificación',
        'deleted'   => 'Eliminación',
        'restored'  => 'Restauración',
        'sync'      => 'Cambio de relación',
        'attach'    => 'Asignación',
        'detach'    => 'Quita de relación',
    ];

    public function __construct()
    {
        $this->middleware('can:' . Permiso::VER_AUDITORIA);
    }

    /**
     * Muestra la pantalla del historial de acciones (auditoría).
     */
    public function index()
    {
        $entidades = self::ENTIDADES;
        $eventos = self::EVENTOS;

        return view('admin.auditoria.index', compact('entidades', 'eventos'));
    }

    /**
     * Proveedor de datos para la tabla del historial (server-side, Yajra DataTables).
     */
    public function getData(Request $request)
    {
        $query = Audit::query()
            ->with('user')
            ->select([
                'id',
                'user_type',
                'user_id',
                'event',
                'auditable_type',
                'auditable_id',
                'old_values',
                'new_values',
                'url',
                'ip_address',
                'user_agent',
                'created_at',
            ]);

        // Filtro por entidad (clase del modelo auditado).
        if ($request->filled('entidad')) {
            $query->where('auditable_type', $request->input('entidad'));
        }

        // Filtro por tipo de acción (evento).
        if ($request->filled('evento')) {
            $query->where('event', $request->input('evento'));
        }

        // Filtro por rango de fechas (sobre created_at).
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->input('fecha_desde'));
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->input('fecha_hasta'));
        }

        return DataTables::of($query)
            ->editColumn('created_at', fn(Audit $a) => optional($a->created_at)->format('d/m/Y H:i:s'))
            ->addColumn('usuario', function (Audit $a) {
                if (!$a->user) {
                    return '<em class="text-gray-400">Sistema / sin sesión</em>';
                }
                // Los usuarios de laboratorio se crean con un apellido placeholder
                // ('Laboratorio_ap_paterno'); se omite para mostrar un nombre limpio.
                $partes = array_filter(
                    [$a->user->nombre, $a->user->ap_paterno, $a->user->ap_materno],
                    fn($parte) => filled($parte) && $parte !== 'Laboratorio_ap_paterno'
                );
                $nombre = trim(implode(' ', $partes));
                $nombre = $nombre !== '' ? $nombre : $a->user->username;
                return e($nombre) . ' <span class="text-gray-400 text-xs">(' . e($a->user->username) . ')</span>';
            })
            ->addColumn('entidad', fn(Audit $a) => e(self::ENTIDADES[$a->auditable_type] ?? class_basename($a->auditable_type)))
            ->addColumn('evento_label', function (Audit $a) {
                $etiqueta = self::EVENTOS[$a->event] ?? ucfirst($a->event);
                $color = match ($a->event) {
                    'created'  => 'bg-green-100 text-green-800',
                    'updated'  => 'bg-yellow-100 text-yellow-800',
                    'deleted'  => 'bg-red-100 text-red-800',
                    'restored' => 'bg-blue-100 text-blue-800',
                    'sync', 'attach', 'detach' => 'bg-purple-100 text-purple-800',
                    default    => 'bg-gray-100 text-gray-800',
                };
                return "<span class='inline-flex items-center px-2 py-0.5 text-xs font-medium rounded $color'>" . e($etiqueta) . "</span>";
            })
            ->addColumn('cambios', fn(Audit $a) => $this->formatearCambios($a))
            ->rawColumns(['usuario', 'entidad', 'evento_label', 'cambios'])
            ->make(true);
    }

    /**
     * Construye una representación HTML de los valores anteriores y nuevos.
     */
    private function formatearCambios(Audit $audit): string
    {
        $viejos = $audit->old_values ?? [];
        $nuevos = $audit->new_values ?? [];
        $campos = array_unique(array_merge(array_keys($viejos), array_keys($nuevos)));

        if (empty($campos)) {
            return '<span class="text-gray-400">-</span>';
        }

        $filas = '';
        foreach ($campos as $campo) {
            $antes = $this->normalizarValor($viejos[$campo] ?? null);
            $despues = $this->normalizarValor($nuevos[$campo] ?? null);
            $filas .= '<tr>'
                . '<td class="pr-3 font-medium text-gray-700 align-top">' . e($campo) . '</td>'
                . '<td class="pr-3 text-red-600 align-top">' . $antes . '</td>'
                . '<td class="text-green-700 align-top">' . $despues . '</td>'
                . '</tr>';
        }

        return '<table class="text-xs border-collapse">'
            . '<thead><tr class="text-gray-500"><th class="pr-3 text-left">Campo</th><th class="pr-3 text-left">Antes</th><th class="text-left">Después</th></tr></thead>'
            . '<tbody>' . $filas . '</tbody></table>';
    }

    /**
     * Convierte un valor a texto legible para mostrarlo en la tabla.
     */
    private function normalizarValor($valor): string
    {
        if (is_null($valor)) {
            return '<span class="text-gray-400">vacío</span>';
        }
        if (is_bool($valor)) {
            return $valor ? 'true' : 'false';
        }
        if (is_array($valor)) {
            return e(json_encode($valor, JSON_UNESCAPED_UNICODE));
        }
        return e((string) $valor);
    }
}
