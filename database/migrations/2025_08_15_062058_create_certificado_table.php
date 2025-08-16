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
            $table->integer('id_certificado', false, true)->primary();
            $table->unsignedBigInteger('id_inscripcion');
            $table->integer('gestion_certificado')->unsigned();
            $table->string('nombre_coordinador', 200);
            $table->string('nombre_jefe', 200);
            $table->string('nombre_director', 200);
            $table->string('firma_coordinador', 200);
            $table->string('firma_jefe', 100);
            $table->string('firma_director', 100);
            $table->string('nombre_laboratorio', 400);
            $table->string('codigo_certificado', 10);
            $table->tinyInteger('tipo_certificado');
            $table->integer('id_redaccion')->unsigned();
            $table->tinyInteger('status_certificado');
            $table->dateTime('created_at')->useCurrent();
            $table->integer('created_by')->unsigned();
            $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate();
            $table->integer('updated_by')->unsigned()->nullable();
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
