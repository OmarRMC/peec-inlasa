<?php

namespace App\Models;

use App\Traits\General;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permiso extends Model
{
    use General;

    public const ADMIN = 'admin';
    // Escritorio
    public const VER_ESCRITORIO = 'ver.escritorio';

    // Inscripciones
    public const VER_INSCRIPCIONES = 'ver.inscripciones';
    public const EDITAR_INSCRIPCIONES = 'editar.inscripciones';
    public const VER_FORMULARIOS = 'ver.formularios';
    public const EDITAR_FORMULARIOS = 'editar.formularios';
    public const VER_DOCUMENTOS = 'ver.documentos';
    public const EDITAR_DOCUMENTOS = 'editar.documentos';

    // Certificados
    public const VER_CERTIFICADOS_PARTICIPACION = 'ver.certificados.participacion';
    public const EDITAR_CERTIFICADOS_PARTICIPACION = 'editar.certificados.participacion';
    public const VER_CERTIFICADOS_DESEMPENO = 'ver.certificados.desempeno';
    public const EDITAR_CERTIFICADOS_DESEMPENO = 'editar.certificados.desempeno';

    // Configuración
    public const VER_CONFIGURACION = 'ver.configuracion';
    public const EDITAR_CONFIGURACION = 'editar.configuracion';

    // Recursos Laboratorio
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

    // Gestión de Laboratorio
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

    // Programas
    public const VER_PROGRAMAS = 'ver.programas';
    public const EDITAR_PROGRAMAS = 'editar.programas';
    public const VER_AREA = 'ver.area';
    public const EDITAR_AREA = 'editar.area';
    public const VER_PAQUETES = 'ver.paquetes';
    public const EDITAR_PAQUETES = 'editar.paquetes';
    public const VER_ENSAYOS_APTITUD = 'ver.ensayos.aptitud';
    public const EDITAR_ENSAYOS_APTITUD = 'editar.ensayos.aptitud';

    // Ubicación geográfica
    public const VER_PAIS = 'ver.pais';
    public const EDITAR_PAIS = 'editar.pais';
    public const VER_DEPARTAMENTO = 'ver.departamento';
    public const EDITAR_DEPARTAMENTO = 'editar.departamento';
    public const VER_PROVINCIA = 'ver.provincia';
    public const EDITAR_PROVINCIA = 'editar.provincia';
    public const VER_MUNICIPIO = 'ver.municipio';
    public const EDITAR_MUNICIPIO = 'editar.municipio';

    // Usuarios y Roles
    public const VER_USUARIOS = 'ver.usuarios';
    public const EDITAR_USUARIOS = 'editar.usuarios';
    public const VER_CARGOS = 'ver.cargos';
    public const EDITAR_CARGOS = 'editar.cargos';
    public const VER_PERMISOS = 'ver.permisos';
    public const EDITAR_PERMISOS = 'editar.permisos';

    // Manual y Acerca de
    public const VER_MANUAL_USUARIO = 'ver.manual.usuario';
    public const VER_ACERCA_DE = 'ver.acerca.de';
    public const LABORATORIO = 'laboratorio';
    public const RESPONSABLE = 'responsable';

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
