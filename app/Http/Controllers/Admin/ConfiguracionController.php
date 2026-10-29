<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AprobarInscripcion;
use App\Mail\EnvioObsLab;
use App\Models\Configuracion;
use App\Models\Laboratorio;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Visibility;

class ConfiguracionController extends Controller
{

    public function __construct()
    {
        $this->middleware('canany:' . Permiso::CONFIGURACION . ',' . Permiso::ADMIN)->only(['index']);
    }
    public function index()
    {
        $configuraciones = Configuracion::all()->pluck('valor', 'clave');

        return view('config.index', compact('configuraciones'));
    }

    public function update(Request $request, $seccion)
    {
        if (!Gate::any([Permiso::GESTION_CERTIFICADOS, Permiso::ADMIN, Permiso::CONFIGURACION])) {
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección.');
        }
        switch ($seccion) {
            case 'periodo-inscripcion':
                $request->validate([
                    'fecha_inicio_inscripcion' => 'required|date',
                    'fecha_fin_inscripcion' => 'required|date|after_or_equal:fecha_inicio_inscripcion',
                    'gestion_inscripcion' => 'required|digits:4'
                ], [
                    'fecha_inicio_inscripcion.required' => 'La fecha de inicio es obligatoria.',
                    'fecha_inicio_inscripcion.date' => 'La fecha de inicio debe ser una fecha válida.',
                    'fecha_fin_inscripcion.required' => 'La fecha de fin es obligatoria.',
                    'fecha_fin_inscripcion.date' => 'La fecha de fin debe ser una fecha válida.',
                    'fecha_fin_inscripcion.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
                    'gestion_inscripcion.required' => 'El campo gestión actual es obligatorio.',
                    'gestion_inscripcion.digits' => 'La gestión debe contener exactamente 4 dígitos.',
                ]);
                configuracion(Configuracion::GESTION_INSCRIPCION, $request->gestion_inscripcion);
                configuracion(Configuracion::FECHA_INICIO_INSCRIPCION, $request->fecha_inicio_inscripcion);
                configuracion(Configuracion::FECHA_FIN_INSCRIPCION, $request->fecha_fin_inscripcion);
                break;
            case 'periodo-registro':
                $request->validate([
                    'fecha_inicio_registro_lab' => 'required|date',
                    'fecha_fin_registro_lab' => 'required|date|after_or_equal:fecha_inicio_registro_lab',
                ], [
                    'fecha_inicio_registro_lab.required' => 'La fecha de inicio es obligatoria.',
                    'fecha_inicio_registro_lab.date' => 'La fecha de inicio debe ser una fecha válida.',
                    'fecha_fin_registro_lab.required' => 'La fecha de fin es obligatoria.',
                    'fecha_fin_registro_lab.date' => 'La fecha de fin debe ser una fecha válida.',
                    'fecha_fin_registro_lab.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
                ]);
                configuracion(Configuracion::FECHA_INICIO_REGISTRO_NUEVO_LAB, $request->fecha_inicio_registro_lab);
                configuracion(Configuracion::FECHA_FIN_REGISTRO_NUEVO_LAB, $request->fecha_fin_registro_lab);
                break;
            case 'periodo-pago':
                $request->validate([
                    'fecha_inicio_pago' => 'required|date',
                    'fecha_fin_pago' => 'required|date|after_or_equal:fecha_inicio_pago',
                ], [
                    'fecha_inicio_pago.required' => 'La fecha de inicio es obligatoria.',
                    'fecha_inicio_pago.date' => 'La fecha de inicio debe ser una fecha válida.',
                    'fecha_fin_pago.required' => 'La fecha de fin es obligatoria.',
                    'fecha_fin_pago.date' => 'La fecha de fin debe ser una fecha válida.',
                    'fecha_fin_pago.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
                ]);
                configuracion(Configuracion::FECHA_INICIO_PAGO, $request->fecha_inicio_pago);
                configuracion(Configuracion::FECHA_FIN_PAGO, $request->fecha_fin_pago);
                break;

            case 'periodo-vigencia':
                $request->validate([
                    'fecha_inicio_vigencia' => 'required|date',
                    'fecha_fin_vigencia' => 'required|date|after_or_equal:fecha_inicio_vigencia',
                ], [
                    'fecha_inicio_vigencia.required' => 'La fecha de inicio es obligatoria.',
                    'fecha_inicio_vigencia.date' => 'La fecha de inicio debe ser una fecha válida.',
                    'fecha_fin_vigencia.required' => 'La fecha de fin es obligatoria.',
                    'fecha_fin_vigencia.date' => 'La fecha de fin debe ser una fecha válida.',
                    'fecha_fin_vigencia.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
                ]);

                configuracion(Configuracion::FECHA_INICIO_VIGENCIA, $request->fecha_inicio_vigencia);
                configuracion(Configuracion::FECHA_FIN_VIGENCIA, $request->fecha_fin_vigencia);
                break;

            case 'notificacion':
                $request->validate([
                    'notificacion_key' => 'required|string',
                    'notificacion_titulo' => 'required|string|max:100',
                    'notificacion_descripcion' => 'nullable|string|max:255',
                    'notificacion_mensaje' => 'nullable|string',
                    'notificacion_fecha_inicio' => 'required|date',
                    'notificacion_fecha_fin' => 'required|date|after_or_equal:notificacion_fecha_inicio',
                ], [
                    'notificacion_key.required' => 'El campo "key" es obligatorio.',
                    'notificacion_key.string' => 'El campo "key" debe ser una cadena de texto.',
                    'notificacion_titulo.required' => 'El título es obligatorio.',
                    'notificacion_titulo.string' => 'El título debe ser una cadena de texto.',
                    'notificacion_titulo.max' => 'El título no debe exceder los 100 caracteres.',
                    'notificacion_descripcion.string' => 'La descripción debe ser una cadena de texto.',
                    'notificacion_descripcion.max' => 'La descripción no debe exceder los 255 caracteres.',
                    'notificacion_mensaje.string' => 'El mensaje debe ser una cadena de texto.',
                    'notificacion_fecha_fin.required' => 'La fecha de fin es obligatoria.',
                    'notificacion_fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
                ]);


                configuracion(Configuracion::NOTIFICACION_KEY, $request->notificacion_key);
                configuracion(Configuracion::NOTIFICACION_TITULO, $request->notificacion_titulo);
                configuracion(Configuracion::NOTIFICACION_DESCRIPCION, $request->notificacion_descripcion);
                configuracion(Configuracion::NOTIFICACION_MENSAJE, $request->notificacion_mensaje);
                configuracion(Configuracion::NOTIFICACION_FECHA_INICIO, $request->notificacion_fecha_inicio);
                configuracion(Configuracion::NOTIFICACION_FECHA_FIN, $request->notificacion_fecha_fin);
                break;

            case Configuracion::EMAIL_APROBACION_INFORMACION:
            case Configuracion::EMAIL_OBSERVACION_INFORMACION:
                $campo = str_replace('.', '_', $seccion);
                $request->validate([
                    $campo => 'required|string|max:100000',
                ], [
                    "$campo.required" => 'El contenido de la sección INFORMACIÓN es obligatorio.',
                    "$campo.string" => 'El contenido debe ser una cadena de texto.',
                    "$campo.max" => 'El contenido es demasiado grande (máximo 100.000 caracteres).',
                ]);

                $html = $request->input($campo);
                if ($error = $this->errorEstructuraHtml($html)) {
                    return back()->withInput()->with('error', $error);
                }

                configuracion($seccion, $this->limpiarHtmlEmail($html));
                Log::info('config.' . $seccion, ['user_id' => Auth::id()]);
                break;

            case 'certificados':
                $request->validate([
                    'evaluacion_externa' => 'required|array',
                    'evaluacion_externa.nombre' => 'required|string|max:200',
                    'evaluacion_externa.cargo' => 'required|string|max:200',
                    // 'evaluacion_externa.institucion' => 'required|string|max:200',
                    'evaluacion_externa.imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'coordinadora_red' => 'required|array',
                    'coordinadora_red.nombre' => 'required|string|max:200',
                    'coordinadora_red.cargo' => 'required|string|max:200',
                    // 'coordinadora_red.institucion' => 'required|string|max:200',
                    'coordinadora_red.imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'directora_general' => 'required|array',
                    'directora_general.nombre' => 'required|string|max:200',
                    'directora_general.cargo' => 'required|string|max:200',
                    // 'directora_general.institucion' => 'required|string|max:200',
                    'directora_general.imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ], [
                    'evaluacion_externa.required' => 'Los datos de Evaluación Externa son obligatorios.',
                    'evaluacion_externa.nombre.required' => 'El nombre de Evaluación Externa es obligatorio.',
                    'evaluacion_externa.cargo.required' => 'El cargo de Evaluación Externa es obligatorio.',
                    // 'evaluacion_externa.institucion.required' => 'La institución de Evaluación Externa es obligatoria.',
                    'evaluacion_externa.imagen.image' => 'La imagen de Evaluación Externa debe ser una imagen válida.',
                    'coordinadora_red.required' => 'Los datos de Coordinadora de Red son obligatorios.',
                    'coordinadora_red.nombre.required' => 'El nombre de Coordinadora de Red es obligatorio.',
                    'coordinadora_red.cargo.required' => 'El cargo de Coordinadora de Red es obligatorio.',
                    // 'coordinadora_red.institucion.required' => 'La institución de Coordinadora de Red es obligatoria.',
                    'coordinadora_red.imagen.image' => 'La imagen de Coordinadora de Red debe ser una imagen válida.',
                    'directora_general.required' => 'Los datos de Directora General son obligatorios.',
                    'directora_general.nombre.required' => 'El nombre de Directora General es obligatorio.',
                    'directora_general.cargo.required' => 'El cargo de Directora General es obligatorio.',
                    // 'directora_general.institucion.required' => 'La institución de Directora General es obligatoria.',
                    'directora_general.imagen.image' => 'La imagen de Directora General debe ser una imagen válida.',
                ]);

                $actuales = [
                    Configuracion::CARGO_EVALUACION_EXTERNA => configuracion(Configuracion::CARGO_EVALUACION_EXTERNA) ?? (object) [],
                    Configuracion::CARGO_COORDINADORA_RED  => configuracion(Configuracion::CARGO_COORDINADORA_RED) ?? (object) [],
                    Configuracion::CARGO_DIRECTORA_GENERAL => configuracion(Configuracion::CARGO_DIRECTORA_GENERAL) ?? (object) [],
                ];

                $nuevos = [
                    Configuracion::CARGO_EVALUACION_EXTERNA => $request->evaluacion_externa,
                    Configuracion::CARGO_COORDINADORA_RED  => $request->coordinadora_red,
                    Configuracion::CARGO_DIRECTORA_GENERAL => $request->directora_general,
                ];

                foreach ($nuevos as $clave => $datosNuevos) {
                    $datosActuales = $actuales[$clave] ?? (object) [];

                    if (isset($datosNuevos['imagen']) && $datosNuevos['imagen'] instanceof \Illuminate\Http\UploadedFile) {
                        $folder = str_replace('_', '-', strtolower($clave));
                        $extension = strtolower($datosNuevos['imagen']->getClientOriginalExtension());
                        $nombreLimpio = preg_replace('/[^A-Za-z0-9_\-]/', '_', $datosNuevos['nombre']);
                        $nombreArchivo = $nombreLimpio . '.' . $extension;

                        $path = Storage::disk('public')->putFileAs(
                            "firmas/{$folder}",
                            $datosNuevos['imagen'],
                            $nombreArchivo,
                            ['visibility' => Visibility::PUBLIC]
                        );

                        $datosNuevos['imagen'] = "/storage/{$path}";
                    } else {
                        $datosNuevos['imagen'] = $datosActuales->imagen ?? null;
                    }
                    $arrActuales = (array) $datosActuales;
                    if ($datosNuevos != $arrActuales) {
                        $datosFinales = array_merge($arrActuales, $datosNuevos);
                        configuracion($clave, $datosFinales);
                    }
                }
                break;
            case 'periodo.registro.desemp':
                $request->validate([
                    'fecha_inicio_registro_certificados' => 'required|date',
                    'fecha_fin_registro_certificados' => 'required|date|after_or_equal:fecha_inicio_registro_certificados',
                ], [
                    'fecha_inicio_registro_certificados.required' => 'La fecha de inicio es obligatoria.',
                    'fecha_inicio_registro_certificados.date' => 'La fecha de inicio debe ser una fecha válida.',
                    'fecha_fin_registro_certificados.required' => 'La fecha de fin es obligatoria.',
                    'fecha_fin_registro_certificados.date' => 'La fecha de fin debe ser una fecha válida.',
                    'fecha_fin_registro_certificados.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
                    'registro_ponderaciones_certificados_gestion.required' => 'El campo gestión actual es obligatorio.',
                    'registro_ponderaciones_certificados_gestion.digits' => 'La gestión debe contener exactamente 4 dígitos.',
                ]);

                configuracion(Configuracion::FECHA_INICIO_REGISTRO_CERTIFICADOS, $request->fecha_inicio_registro_certificados);
                configuracion(Configuracion::FECHA_FIN_REGISTRO_CERTIFICADOS, $request->fecha_fin_registro_certificados);
                // configuracion(Configuracion::REGISTRO_PONDERACIONES_CERTIFICADOS_GESTION, $request->registro_ponderaciones_certificados_gestion);
                break;
            case 'gestion.filter':
                $request->validate([
                    'key_gestion_filter' => [
                        'required',
                        'regex:/^\d{4}(,\d{4})*$/',
                    ],
                ], [
                    'key_gestion_filter.required' => 'Es requerido',
                    'key_gestion_filter.regex' => 'Debe ser años de 4 dígitos separados por coma (ej. 2025,2024)',
                ]);
                $gestiones = explode(',', $request->key_gestion_filter);
                $gestiones = array_map('intval', $gestiones);
                configuracion(Configuracion::KEY_GESTION_FILTER, json_encode($gestiones));
                break;
            case 'habilitar.docs.inscripcion':
                $habilitar = !!$request->has(Configuracion::HABILITAR_SUBIDA_DOCUMENTOS_INSCRIPCION);
                configuracion(Configuracion::HABILITAR_SUBIDA_DOCUMENTOS_INSCRIPCION, $habilitar);
                break;
            case 'habilitar.docs.pagos':
                $habilitar = !!$request->has(Configuracion::HABILITAR_SUBIDA_DOCUMENTOS_PAGOS);
                configuracion(Configuracion::HABILITAR_SUBIDA_DOCUMENTOS_PAGOS, $habilitar);
                break;
            default:
                return redirect()->back()->with('error', 'Error en registrar la Configuración.');
        }

        return redirect()->back()->with('success', 'Configuración actualizada correctamente.');
    }

    public function certificados()
    {
        if (!Gate::any([Permiso::GESTION_CERTIFICADOS, Permiso::ADMIN])) {
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección.');
        }
        $configuraciones = Configuracion::whereIn('key', [
            Configuracion::CARGO_DIRECTORA_GENERAL,
            Configuracion::CARGO_COORDINADORA_RED,
            Configuracion::CARGO_EVALUACION_EXTERNA
        ])
            ->pluck('valor', 'key')
            ->map(function ($valor, $clave) {
                $obj = json_decode($valor);
                $obj->clave = $clave;
                return $obj;
            })
            ->values();
        return view('certificados.config', compact('configuraciones'));
    }

    /**
     * Vista previa del correo completo (aprobación u observación) en una pestaña nueva.
     * Muestra el contenido actual del editor (no se guarda).
     */
    public function previewEmail(Request $request, string $tipo)
    {
        if (!Gate::any([Permiso::ADMIN, Permiso::CONFIGURACION])) {
            abort(403);
        }

        $html = $this->limpiarHtmlEmail((string) $request->input('html', ''));
        $aviso = $this->errorEstructuraHtml((string) $request->input('html', ''));

        $lab = Laboratorio::with('usuario')->whereHas('usuario')->find($request->input('laboratorio_id'))
            ?? Laboratorio::with('usuario')->whereHas('usuario')->active()->orderBy('cod_lab')->first();
        if (!$lab) {
            abort(404, 'No hay laboratorios para la vista previa.');
        }
        $gestion = $lab->inscripciones()->latest('id')->value('gestion')
            ?? configuracion(Configuracion::GESTION_INSCRIPCION);

        $mail = $tipo === 'aprobacion'
            ? new AprobarInscripcion($lab->usuario, $lab, $gestion, $html)
            : new EnvioObsLab($lab->usuario, $lab, [
                'Formulario de inscripción' => 'Ejemplo: falta la firma del responsable.',
                'Comprobante de pago' => 'Ejemplo: el documento no es legible.',
            ], $gestion, $html);

        $banner = view('config.partials.email_preview_banner', [
            'asunto' => $mail->envelope()->subject,
            'lab' => $lab,
            'gestion' => $gestion,
            'aviso' => $aviso,
        ])->render();

        $contenido = preg_replace('/<body[^>]*>/i', '$0' . $banner, $mail->render(), 1);

        return response($contenido);
    }

    /** Buscador de laboratorios por código (o nombre) para la vista previa. */
    public function buscarLaboratorioEmail(Request $request)
    {
        if (!Gate::any([Permiso::ADMIN, Permiso::CONFIGURACION])) {
            abort(403);
        }
        $q = trim((string) $request->input('q', ''));

        return Laboratorio::whereHas('usuario')
            ->when($q !== '', fn($query) => $query->where(
                fn($w) => $w->where('cod_lab', 'like', "%$q%")->orWhere('nombre_lab', 'like', "%$q%")
            ))
            ->orderBy('cod_lab')
            ->limit(15)
            ->get(['id', 'cod_lab', 'nombre_lab']);
    }

    /** Subida de imágenes desde el editor visual (formato de respuesta de Jodit). */
    public function subirImagenEmail(Request $request)
    {
        if (!Gate::any([Permiso::ADMIN, Permiso::CONFIGURACION])) {
            return response()->json(['success' => false, 'data' => ['messages' => ['No tiene permiso.']]], 403);
        }

        $validator = validator($request->all(), [
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'imagen.required' => 'Seleccione una imagen.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'Formatos permitidos: jpg, png, gif, webp.',
            'imagen.max' => 'La imagen no debe superar los 2 MB.',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'data' => ['messages' => $validator->errors()->all()]]);
        }

        $path = $request->file('imagen')->store('emails', 'public');
        Log::info('config.email.imagen', ['user_id' => Auth::id(), 'path' => $path]);

        // URL absoluta: el correo se abre fuera del sistema
        return response()->json(['success' => true, 'data' => [
            'baseurl' => url('storage') . '/',
            'files' => [$path],
            'isImages' => [true],
            'messages' => [],
        ]]);
    }

    /** Devuelve un mensaje si las etiquetas de tabla/lista no están balanceadas (rompería el diseño del correo). */
    private function errorEstructuraHtml(string $html): ?string
    {
        $html = preg_replace('/<!--.*?-->/s', '', $html);
        foreach (['table', 'tr', 'td', 'th', 'ul', 'ol', 'div', 'a'] as $tag) {
            $abiertas = preg_match_all('/<' . $tag . '(\s[^>]*)?>/i', $html);
            $cerradas = preg_match_all('/<\/' . $tag . '\s*>/i', $html);
            if ($abiertas !== $cerradas) {
                return "El HTML tiene etiquetas <$tag> sin cerrar o de más ($abiertas abiertas, $cerradas cerradas). Revise el código fuente.";
            }
        }
        return null;
    }

    /** Quita scripts, iframes, formularios y atributos peligrosos; conserva estilos en línea y <style>. */
    private function limpiarHtmlEmail(string $html): string
    {
        $doc = new \DOMDocument();
        $prev = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8"?><div id="__raiz">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $xpath = new \DOMXPath($doc);
        foreach ($xpath->query('//script|//iframe|//form|//object|//embed|//input|//button|//textarea|//select|//meta|//link|//base') as $nodo) {
            $nodo->parentNode->removeChild($nodo);
        }
        foreach ($xpath->query('//@*') as $attr) {
            $nombre = strtolower($attr->nodeName);
            $valor = strtolower(preg_replace('/\s+/', '', $attr->nodeValue));
            if (str_starts_with($nombre, 'on')
                || (in_array($nombre, ['href', 'src', 'action', 'formaction', 'xlink:href'])
                    && (str_starts_with($valor, 'javascript:') || str_starts_with($valor, 'vbscript:') || str_starts_with($valor, 'data:text')))
            ) {
                $attr->ownerElement->removeAttributeNode($attr);
            }
        }

        $raiz = $doc->getElementById('__raiz') ?? $xpath->query('//div[@id="__raiz"]')->item(0);
        $salida = '';
        foreach ($raiz->childNodes as $hijo) {
            $salida .= $doc->saveHTML($hijo);
        }
        // libxml codifica las llaves en href/src: restaurar los comodines ({codigo} → %7Bcodigo%7D → {codigo})
        return trim(preg_replace('/%7B(\w+)%7D/i', '{$1}', $salida));
    }
}
