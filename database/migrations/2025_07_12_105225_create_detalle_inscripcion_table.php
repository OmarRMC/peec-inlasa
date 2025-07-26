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
        Schema::create('detalle_inscripcion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_inscripcion');
            $table->unsignedBigInteger('id_paquete');
            $table->string('descripcion_paquete', 100);
            $table->integer('costo_paquete');
            $table->string('observaciones');
            $table->foreign('id_inscripcion')->references('id')->on('inscripcion');
            $table->foreign('id_paquete')->references('id')->on('paquete');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_inscripcion');
    }
};
