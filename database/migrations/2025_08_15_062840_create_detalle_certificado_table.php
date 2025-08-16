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
        Schema::create('detalle_certificado', function (Blueprint $table) {
            //$table->id();
            $table->integer('id_detalle_certificado', false, true)->primary();
            $table->integer('id_certificado')->unsigned();
            $table->integer('id_paquete')->unsigned();
            $table->integer('id_parametro')->unsigned();
            $table->string('detalle_parametro', 400);
            $table->string('calificacion_certificado', 100)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_certificado');
    }
};
