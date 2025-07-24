@extends('emails.layout')

@section('content')
    <div style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 30px;">
        <div style="background-color: #fff; padding: 20px 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">

            <h2 style="color: #2c3e50; margin-bottom: 10px;">✔ Verificación de Registro de Laboratorio</h2>

            <p style="font-size: 16px; color: #333;">Estimado(a) <strong>{{ $laboratorio->respo_lab }}</strong>,</p>

            <p style="font-size: 16px; color: #333;">
                Gracias por registrar el laboratorio <strong>{{ $laboratorio->nombre_lab }}</strong>.
                A continuación, se detallan los datos proporcionados. Por favor, revíselos cuidadosamente:
            </p>

            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <tbody>
                    <tr>
                        <td style="padding: 8px; font-weight: bold;">Sigla:</td>
                        <td style="padding: 8px;">{{ $laboratorio->sigla_lab ?? '---' }}</td>
                    </tr>
                    <tr style="background-color: #f0f0f0;">
                        <td style="padding: 8px; font-weight: bold;">Código PEEC Anterior:</td>
                        <td style="padding: 8px;">{{ $laboratorio->antcod_peec ?? '---' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; font-weight: bold;">N° de Sedes:</td>
                        <td style="padding: 8px;">{{ $laboratorio->numsedes_lab ?? '---' }}</td>
                    </tr>
                    <tr style="background-color: #f0f0f0;">
                        <td style="padding: 8px; font-weight: bold;">NIT:</td>
                        <td style="padding: 8px;">{{ $laboratorio->nit_lab ?? '---' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; font-weight: bold;">País:</td>
                        <td style="padding: 8px;">{{ $pais }}</td>
                    </tr>
                    <tr style="background-color: #f0f0f0;">
                        <td style="padding: 8px; font-weight: bold;">Departamento:</td>
                        <td style="padding: 8px;">{{ $departamento }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; font-weight: bold;">Provincia:</td>
                        <td style="padding: 8px;">{{ $provincia }}</td>
                    </tr>
                    <tr style="background-color: #f0f0f0;">
                        <td style="padding: 8px; font-weight: bold;">Municipio:</td>
                        <td style="padding: 8px;">{{ $municipio }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; font-weight: bold;">Zona:</td>
                        <td style="padding: 8px;">{{ $laboratorio->zona_lab }}</td>
                    </tr>
                    <tr style="background-color: #f0f0f0;">
                        <td style="padding: 8px; font-weight: bold;">Dirección:</td>
                        <td style="padding: 8px;">{{ $laboratorio->direccion_lab }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; font-weight: bold;">Responsable:</td>
                        <td style="padding: 8px;">{{ $laboratorio->respo_lab }} (CI:
                            {{ $laboratorio->ci_respo_lab ?? '---' }})</td>
                    </tr>
                    <tr style="background-color: #f0f0f0;">
                        <td style="padding: 8px; font-weight: bold;">Representante Legal:</td>
                        <td style="padding: 8px;">{{ $laboratorio->repreleg_lab }} (CI:
                            {{ $laboratorio->ci_repreleg_lab ?? '---' }})</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; font-weight: bold;">Categoría:</td>
                        <td style="padding: 8px;">{{ $categoria }}</td>
                    </tr>
                    <tr style="background-color: #f0f0f0;">
                        <td style="padding: 8px; font-weight: bold;">Tipo:</td>
                        <td style="padding: 8px;">{{ $tipo }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; font-weight: bold;">Nivel:</td>
                        <td style="padding: 8px;">{{ $nivel }}</td>
                    </tr>
                    <tr style="background-color: #f0f0f0;">
                        <td style="padding: 8px; font-weight: bold;">Whatsapp 1:</td>
                        <td style="padding: 8px;">{{ $laboratorio->wapp_lab }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; font-weight: bold;">Whatsapp 2:</td>
                        <td style="padding: 8px;">{{ $laboratorio->wapp2_lab ?? '---' }}</td>
                    </tr>
                    <tr style="background-color: #f0f0f0;">
                        <td style="padding: 8px; font-weight: bold;">Correo Principal:</td>
                        <td style="padding: 8px;">{{ $laboratorio->mail_lab }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; font-weight: bold;">Correo Alternativo:</td>
                        <td style="padding: 8px;">{{ $laboratorio->mail2_lab ?? '---' }}</td>
                    </tr>
                    <tr style="background-color: #f0f0f0;">
                        <td style="padding: 8px; font-weight: bold;">Teléfono:</td>
                        <td style="padding: 8px;">{{ $laboratorio->telefono ?? '---' }}</td>
                    </tr>
                </tbody>
            </table>

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ route('confirmar.datos.lab', $laboratorio->id) }}"
                    style="display: inline-block; padding: 12px 20px; background-color: #2ecc71; color: white; text-decoration: none; border-radius: 5px; font-size: 16px;">
                    ✔ Confirmar Registro
                </a>
            </div>

            <p style="margin-top: 30px; font-size: 14px; color: #888;">Si usted no realizó este registro, puede ignorar este
                mensaje.</p>
        </div>
    </div>
@endsection
