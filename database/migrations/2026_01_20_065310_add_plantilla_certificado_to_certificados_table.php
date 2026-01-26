<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('certificado', function (Blueprint $table) {
            $table->foreignId('plantilla_certificado_id')
                ->nullable()
                ->constrained('plantillas_certificados')
                ->onDelete('set null');
            $table->dropColumn([
                'nombre_coordinador',
                'nombre_jefe',
                'nombre_director',
                'firma_coordinador',
                'firma_jefe',
                'firma_director',
                'cargo_coordinador',
                'cargo_jefe',
                'cargo_director',
                'id_redaccion',
                'tipo_certificado',
                'codigo_certificado'
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('certificado', function (Blueprint $table) {
            $table->dropForeign(['plantilla_certificado_id']);
            $table->dropColumn('plantilla_certificado_id');
            $table->string('nombre_coordinador', 200)->nullable();
            $table->string('nombre_jefe', 200)->nullable();
            $table->string('nombre_director', 200)->nullable();
            $table->string('firma_coordinador', 200)->nullable();
            $table->string('firma_jefe', 100)->nullable();
            $table->string('firma_director', 100)->nullable();
            $table->string('cargo_coordinador', 200)->nullable();
            $table->string('cargo_jefe', 200)->nullable();
            $table->string('cargo_director', 200)->nullable();
            $table->unsignedBigInteger('id_redaccion')->nullable();
            $table->tinyInteger('tipo_certificado')->nullable();
            $table->string('codigo_certificado', 50)->nullable();
        });
    }
};
