<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configuracion_certificado', function (Blueprint $table) {
            $table->id();
            $table->integer('gestion')->unsigned();
            $table->string('nombre_coordinador', 200);
            $table->string('nombre_jefe', 200);
            $table->string('nombre_director', 200);
            $table->string('firma_coordinador', 200);
            $table->string('firma_jefe', 200);
            $table->string('firma_director', 200);
            $table->string('cargo_coordinador', 200)->nullable();
            $table->string('cargo_jefe', 200)->nullable();
            $table->string('cargo_director', 200)->nullable();
            $table->tinyInteger('tipo_certificado')->nullable();
            $table->boolean('activo')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracion_certificado');
    }
};
