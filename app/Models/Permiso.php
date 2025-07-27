<?php

namespace App\Models;

use App\Traits\General;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permiso extends Model
{
    use General;

    // ------------------------------
    // Claves
    // ------------------------------


    public const VER_INSCRIPCIONES = 'ver.inscripciones';
    public const EDITAR_INSCRIPCIONES = 'editar.inscripciones';
    public const VER_FORMULARIOS = 'ver.formularios';
    public const EDITAR_FORMULARIOS = 'editar.formularios';
    public const VER_DOCUMENTOS = 'ver.documentos';
    public const EDITAR_DOCUMENTOS = 'editar.documentos';

    public const VER_CERTIFICADOS_PARTICIPACION = 'ver.certificados.participacion';
    public const EDITAR_CERTIFICADOS_PARTICIPACION = 'editar.certificados.participacion';
    public const VER_CERTIFICADOS_DESEMPENO = 'ver.certificados.desempeno';
    public const EDITAR_CERTIFICADOS_DESEMPENO = 'editar.certificados.desempeno';

    public const VER_CONFIGURACION = 'ver.configuracion';
    public const EDITAR_CONFIGURACION = 'editar.configuracion';

    public const VER_CONTRATO = 'ver.contrato';
    public const EDITAR_CONTRATO = 'editar.contrato';
    public const VER_CONVOCATORIA = 'ver.convocatoria';
    public const EDITAR_CONVOCATORIA = 'editar.convocatoria';
    public const VER_RESOLUCION = 'ver.resolucion';
    public const EDITAR_RESOLUCION = 'editar.resolucion';
    public const VER_PROTOCOLOS = 'ver.protocolos';
    public const EDITAR_PROTOCOLOS = 'editar.protocolos';
    public const VER_QUEJAS = 'ver.quejas';
    public const EDITAR_QUEJAS = 'editar.quejas';
    public const VER_FORMULARIOS_QUEJA = 'ver.formularios.queja';
    public const EDITAR_FORMULARIOS_QUEJA = 'editar.formularios.queja';

    public const VER_NIVEL_LABORATORIO = 'ver.nivel.laboratorio';
    public const EDITAR_NIVEL_LABORATORIO = 'editar.nivel.laboratorio';
    public const VER_TIPO_LABORATORIO = 'ver.tipo.laboratorio';
    public const EDITAR_TIPO_LABORATORIO = 'editar.tipo.laboratorio';
    public const VER_CATEGORIA_LABORATORIO = 'ver.categoria.laboratorio';
    public const EDITAR_CATEGORIA_LABORATORIO = 'editar.categoria.laboratorio';
    public const VER_LABORATORIOS_REGISTRADOS = 'ver.laboratorios.registrados';
    public const EDITAR_LABORATORIOS_REGISTRADOS = 'editar.laboratorios.registrados';
    public const VER_INSCRIPCION_PAQUETE = 'ver.inscripcion.paquete';
    public const EDITAR_INSCRIPCION_PAQUETE = 'editar.inscripcion.paquete';

    public const VER_PROGRAMAS = 'ver.programas';
    public const EDITAR_PROGRAMAS = 'editar.programas';
    public const VER_AREA = 'ver.area';
    public const EDITAR_AREA = 'editar.area';
    public const VER_PAQUETES = 'ver.paquetes';
    public const EDITAR_PAQUETES = 'editar.paquetes';
    public const VER_ENSAYOS_APTITUD = 'ver.ensayos.aptitud';
    public const EDITAR_ENSAYOS_APTITUD = 'editar.ensayos.aptitud';

    public const VER_PAIS = 'ver.pais';
    public const EDITAR_PAIS = 'editar.pais';
    public const VER_DEPARTAMENTO = 'ver.departamento';
    public const EDITAR_DEPARTAMENTO = 'editar.departamento';
    public const VER_PROVINCIA = 'ver.provincia';
    public const EDITAR_PROVINCIA = 'editar.provincia';
    public const VER_MUNICIPIO = 'ver.municipio';
    public const EDITAR_MUNICIPIO = 'editar.municipio';

    public const VER_USUARIOS = 'ver.usuarios';
    public const EDITAR_USUARIOS = 'editar.usuarios';
    public const VER_CARGOS = 'ver.cargos';
    public const EDITAR_CARGOS = 'editar.cargos';
    public const VER_PERMISOS = 'ver.permisos';
    public const EDITAR_PERMISOS = 'editar.permisos';

    public const VER_MANUAL_USUARIO = 'ver.manual.usuario';
    public const VER_ACERCA_DE = 'ver.acerca.de';

    public const RESPONSABLE = 'responsable';
    public const LABORATORIO = 'laboratorio';

    // ------------------------------
    // Nombres legibles
    // ------------------------------

    public const VER_INSCRIPCIONES_NAME = 'Ver inscripciones';
    public const EDITAR_INSCRIPCIONES_NAME = 'Editar inscripciones';
    public const VER_FORMULARIOS_NAME = 'Ver formularios';
    public const EDITAR_FORMULARIOS_NAME = 'Editar formularios';
    public const VER_DOCUMENTOS_NAME = 'Ver documentos';
    public const EDITAR_DOCUMENTOS_NAME = 'Editar documentos';

    public const VER_CERTIFICADOS_PARTICIPACION_NAME = 'Ver certificados de participación';
    public const EDITAR_CERTIFICADOS_PARTICIPACION_NAME = 'Editar certificados de participación';
    public const VER_CERTIFICADOS_DESEMPENO_NAME = 'Ver certificados de desempeño';
    public const EDITAR_CERTIFICADOS_DESEMPENO_NAME = 'Editar certificados de desempeño';

    public const VER_CONFIGURACION_NAME = 'Ver configuración';
    public const EDITAR_CONFIGURACION_NAME = 'Editar configuración';

    public const VER_CONTRATO_NAME = 'Ver contrato';
    public const EDITAR_CONTRATO_NAME = 'Editar contrato';
    public const VER_CONVOCATORIA_NAME = 'Ver convocatoria';
    public const EDITAR_CONVOCATORIA_NAME = 'Editar convocatoria';
    public const VER_RESOLUCION_NAME = 'Ver resolución';
    public const EDITAR_RESOLUCION_NAME = 'Editar resolución';
    public const VER_PROTOCOLOS_NAME = 'Ver protocolos';
    public const EDITAR_PROTOCOLOS_NAME = 'Editar protocolos';
    public const VER_QUEJAS_NAME = 'Ver quejas';
    public const EDITAR_QUEJAS_NAME = 'Editar quejas';
    public const VER_FORMULARIOS_QUEJA_NAME = 'Ver formularios de queja';
    public const EDITAR_FORMULARIOS_QUEJA_NAME = 'Editar formularios de queja';

    public const VER_NIVEL_LABORATORIO_NAME = 'Ver nivel de laboratorio';
    public const EDITAR_NIVEL_LABORATORIO_NAME = 'Editar nivel de laboratorio';
    public const VER_TIPO_LABORATORIO_NAME = 'Ver tipo de laboratorio';
    public const EDITAR_TIPO_LABORATORIO_NAME = 'Editar tipo de laboratorio';
    public const VER_CATEGORIA_LABORATORIO_NAME = 'Ver categoría de laboratorio';
    public const EDITAR_CATEGORIA_LABORATORIO_NAME = 'Editar categoría de laboratorio';
    public const VER_LABORATORIOS_REGISTRADOS_NAME = 'Ver laboratorios registrados';
    public const EDITAR_LABORATORIOS_REGISTRADOS_NAME = 'Editar laboratorios registrados';
    public const VER_INSCRIPCION_PAQUETE_NAME = 'Ver inscripción a paquete';
    public const EDITAR_INSCRIPCION_PAQUETE_NAME = 'Editar inscripción a paquete';

    public const VER_PROGRAMAS_NAME = 'Ver programas';
    public const EDITAR_PROGRAMAS_NAME = 'Editar programas';
    public const VER_AREA_NAME = 'Ver área';
    public const EDITAR_AREA_NAME = 'Editar área';
    public const VER_PAQUETES_NAME = 'Ver paquetes';
    public const EDITAR_PAQUETES_NAME = 'Editar paquetes';
    public const VER_ENSAYOS_APTITUD_NAME = 'Ver ensayos de aptitud';
    public const EDITAR_ENSAYOS_APTITUD_NAME = 'Editar ensayos de aptitud';

    public const VER_PAIS_NAME = 'Ver país';
    public const EDITAR_PAIS_NAME = 'Editar país';
    public const VER_DEPARTAMENTO_NAME = 'Ver departamento';
    public const EDITAR_DEPARTAMENTO_NAME = 'Editar departamento';
    public const VER_PROVINCIA_NAME = 'Ver provincia';
    public const EDITAR_PROVINCIA_NAME = 'Editar provincia';
    public const VER_MUNICIPIO_NAME = 'Ver municipio';
    public const EDITAR_MUNICIPIO_NAME = 'Editar municipio';

    public const VER_USUARIOS_NAME = 'Ver usuarios';
    public const EDITAR_USUARIOS_NAME = 'Editar usuarios';
    public const VER_CARGOS_NAME = 'Ver cargos';
    public const EDITAR_CARGOS_NAME = 'Editar cargos';
    public const VER_PERMISOS_NAME = 'Ver permisos';
    public const EDITAR_PERMISOS_NAME = 'Editar permisos';

    public const VER_MANUAL_USUARIO_NAME = 'Ver manual de usuario';
    public const VER_ACERCA_DE_NAME = 'Ver acerca de';

    public const RESPONSABLE_NAME = 'Responsable';
    public const LABORATORIO_NAME = 'Laboratorio';

    public const VER_ESCRITORIO = 'ver.escritorio';
    public const VER_ESCRITORIO_NAME = 'Ver escritorio';

    public const CONFIGURACION = 'configuracion';
    public const CONFIGURACION_NAME = 'Configuración';

    public const GESTION_USUARIO = 'gestion.usuario';
    public const GESTION_USUARIO_NAME = 'Gestión de usuarios';

    public const GESTION_INSCRIPCIONES = 'gestion.inscripciones';
    public const GESTION_INSCRIPCIONES_NAME = 'Gestión de inscripciones';

    public const GESTION_PAGOS = 'gestion.pagos';
    public const GESTION_PAGOS_NAME = 'Gestión de pagos';

    public const GESTION_LABORATORIO = 'gestion.laboratorio';
    public const GESTION_LABORATORIO_NAME = 'Gestión de laboratorio';

    public const GESTION_PROGRAMAS_AREAS_PAQUETES_EA = 'gestion.programas.areas.paquetes.ea';
    public const GESTION_PROGRAMAS_AREAS_PAQUETES_EA_NAME = 'Gestión de programas, áreas, paquetes y EA';

    public const ADMIN = 'admin';
    public const ADMIN_NAME = 'Administrador';

    public const DELETE_GESTION_PROGRAMAS = 'DELETE_PROGRAMAS_GESTION';

    // ------------------------------
    // PERMISOS_HABILITADOS
    // ------------------------------
    public const PERMISOS_HABILITADOS = [
        self::VER_ESCRITORIO => self::VER_ESCRITORIO_NAME,
        self::CONFIGURACION => self::CONFIGURACION_NAME,
        self::GESTION_USUARIO => self::GESTION_USUARIO_NAME,
        self::GESTION_INSCRIPCIONES => self::GESTION_INSCRIPCIONES_NAME,
        self::GESTION_PAGOS => self::GESTION_PAGOS_NAME,
        self::GESTION_LABORATORIO => self::GESTION_LABORATORIO_NAME,
        self::GESTION_PROGRAMAS_AREAS_PAQUETES_EA => self::GESTION_PROGRAMAS_AREAS_PAQUETES_EA_NAME,
        self::ADMIN => self::ADMIN_NAME,
        self::RESPONSABLE => self::RESPONSABLE_NAME,
        self::LABORATORIO => self::LABORATORIO_NAME,
    ];

    protected $table = 'permiso';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre_permiso',
        'descripcion',
        'status',
    ];

    /**
     * Relación con usuarios
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'usuario_permiso', 'id_permiso', 'id_usuario')->withTimestamps();
    }
}
