 <table class="signatures">
     <tr>
         @forelse ($firmas ?? [] as $firma)
             <td class='firma'>
                 @if (!empty($firma['firma_data_uri']))
                     <img class="signature" src="{{ $firma['firma_data_uri'] }}" alt="Firma">
                 @elseif (!empty($firma['firma']))
                     <img class="signature" src="{{ public_path($firma['firma']) }}" alt="Firma">
                 @endif
             </td>
         @empty
             <td class='firma'></td>
             <td class='firma'></td>
             <td class='firma'></td>
         @endforelse
     </tr>
     <tr>
         @forelse ($firmas ?? [] as $firma)
             <td class='sig'>
                 <div class="name">{{ $firma['nombre'] ?? '' }}</div>
             </td>
         @empty
             <td class='sig'><div class="name"></div></td>
             <td class='sig'><div class="name"></div></td>
             <td class='sig'><div class="name"></div></td>
         @endforelse
     </tr>
     <tr>
         @forelse ($firmas ?? [] as $firma)
             <td class='sig'>
                 <div class="role">{{ $firma['cargo'] ?? '' }}</div>
             </td>
         @empty
             <td class='sig'><div class="role"></div></td>
             <td class='sig'><div class="role"></div></td>
             <td class='sig'><div class="role"></div></td>
         @endforelse
     </tr>
     <tr>
         @forelse ($firmas ?? [] as $firma)
             <td class='sig'>
                 <div class="instituto">INLASA</div>
             </td>
         @empty
             <td class='sig'><div class="instituto">INLASA</div></td>
             <td class='sig'><div class="instituto">INLASA</div></td>
             <td class='sig'><div class="instituto">INLASA</div></td>
         @endforelse
     </tr>
 </table>
 <div class="gestion">Gestión {{ $cert->gestion_certificado }}</div>

 <!-- Pie -->
 <div class="footer">
     @if (!empty($notaTexto))
         {{ $notaTexto }} {{ $cert->gestion_certificado }}.
     @else
         El PEEC INLASA, extiende el presente Certificado únicamente a los Laboratorios Clínicos que cumplieron
         con
         el porcentaje de participación y/o desempeño exigido, según procedimientos internos; respaldados por los
         Informes de Evaluación emitidos durante la gestión {{ $cert->gestion_certificado }}.
     @endif
 </div>

 <!-- QR -->
 <div class="qr">
     <img src="{{ $qr }}" alt="QR">
 </div>
