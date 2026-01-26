<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('plantillas_certificados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->json('descripcion')->nullable();

            $table->text('imagen_fondo')->nullable();

            $table->decimal('ancho_mm', 6, 2)->nullable();   // ej: 297.00
            $table->decimal('alto_mm', 6, 2)->nullable();    // ej: 210.00

            // Diseño del certificado (posiciones, textos, QR, firmas, etc.)
            /*
             * {
             *   "schemaVersion": 1,
             *   "unit": "mm",
             *   "elements": [
             *     { "id":"title", "type":"text", "x":20, "y":40, "w":257, "h":10,
             *       "value":"CERTIFICADO DE PARTICIPACIÓN",
             *       "style": { "fontSize": 18, "fontWeight": 700, "align":"center" }
             *     },
             *     { "id":"qr", "type":"qr", "x":270, "y":175, "size":25, "bind":"data.qr" }
             *   ]
             * }
             * */
            $table->json('diseno')->nullable();
            $table->json('firmas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plantillas_certificados');
    }
};
