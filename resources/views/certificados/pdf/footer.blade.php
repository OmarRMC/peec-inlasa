 <table class="signatures">
     <tr>
         <td class='firma'>
             @if (!empty($cert->firma_jefe))
                 <img class="signature" src="{{ public_path($cert->firma_jefe) }}" alt="Firma Jefe">
             @endif
         </td>
         <td class='firma'>
             @if (!empty($cert->firma_coordinador))
                 <img class="signature" src="{{ public_path($cert->firma_coordinador) }}" alt="Firma Coordinadora">
             @endif
         </td>
         <td class='firma'>
             @if (!empty($cert->firma_director))
                 <img class="signature" src="{{ public_path($cert->firma_director) }}" alt="Firma Directora">
             @endif
         </td>
     </tr>
     <tr>
         <td class='sig'>
             <div class="name">{{ $cert->nombre_jefe }}</div>
         </td>
         <td class='sig'>
             <div class="name">{{ $cert->nombre_director }}</div>
         </td>
         <td class='sig'>
             <div class="name">{{ $cert->nombre_director }}</div>
         </td>
     </tr>
     <tr>
         <td class='sig'>
             <div class="role"> {{ $cert->cargo_jefe ?? 'JEFE PROGRAMA DE EVALUACIÓN EXTERNA DE LA CALIDAD' }}</div>
         </td>
         <td class='sig'>
             <div class="role">
                 {{ $cert->cargo_coordinador ?? 'COORDINADORA DIVISIÓN RED DE LABORATORIOS DE SALUD PÚBLICA' }}
             </div>
         </td>  
         <td class='sig'>
             <div class="role">
                 {{ $cert->cargo_director ?? 'DIRECTORA GENERAL EJECUTIVA' }}
             </div>
         </td>
     </tr>
     <tr>
         <td class='sig'>
             <div class="instituto">INLASA</div>
         </td>
         <td class='sig'>
             <div class="instituto">INLASA</div>
         </td>
         <td class='sig'>
             <div class="instituto">INLASA</div>
         </td>
     </tr>
 </table>
 <div class="gestion">Gestión {{ $cert->gestion_certificado }}</div>

 <!-- Pie -->
 <div class="footer">
     El PEEC INLASA, extiende el presente Certificado únicamente a los Laboratorios Clínicos que cumplieron
     con
     el porcentaje de participación y/o desempeño exigido, según procedimientos internos; respaldados por los
     Informes de Evaluación emitidos durante la gestión {{ $cert->gestion_certificado }}.
 </div>

 <!-- QR -->
 <div class="qr">
     <img src="data:image/png;base64,{{ $qr }}" alt="QR">
 </div>
