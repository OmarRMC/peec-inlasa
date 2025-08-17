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
        Schema::create('certificado', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_inscripcion');
            $table->integer('gestion_certificado')->unsigned();
            $table->string('nombre_coordinador', 200);
            $table->string('nombre_jefe', 200);
            $table->string('nombre_director', 200);
            $table->string('firma_coordinador', 200);
            $table->string('firma_jefe', 100);
            $table->string('firma_director', 100);
            $table->string('nombre_laboratorio', 400);
            $table->string('codigo_certificado', 10)->nullable();
            $table->tinyInteger('tipo_certificado')->nullable();
            $table->unsignedBigInteger('id_redaccion')->nullable();
            $table->tinyInteger('status_certificado')->default(0);
            $table->boolean('publicado')->default(0);
            $table->foreign('id_inscripcion')->references('id')->on('inscripcion')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificado');
    }
};
