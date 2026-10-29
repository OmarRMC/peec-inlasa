{{-- Franja superior de la vista previa del correo (no se envía) --}}
<div style="font-family:arial,helvetica,sans-serif;font-size:13px;background:#fff7cc;color:#333;border-bottom:2px solid #e0b800;padding:10px 16px;line-height:1.5">
    <strong>VISTA PREVIA - correo no enviado</strong>
    (contenido del editor, sin guardar)<br>
    <strong>Asunto:</strong> {{ $asunto }} ·
    <strong>Laboratorio:</strong> {{ $lab->cod_lab }} - {{ $lab->nombre_lab }} ·
    <strong>Gestión:</strong> {{ $gestion }}
    @if ($aviso)
        <div style="margin-top:6px;color:#b00020"><strong>Atención:</strong> {{ $aviso }} No se podrá guardar así.</div>
    @endif
</div>
