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
        Schema::create('seccion_resultados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_formulario_resultado');
            $table->unsignedBigInteger('id_seccion')->nullable();
            $table->string('nombre');
            $table->json('headers')->nullable();

            $table->foreign('id_formulario_resultado')
                ->references('id')->on('formulario_ensayos_resultados')
                ->onDelete('cascade');
            $table->foreign('id_seccion')
                ->references('id')->on('secciones')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seccion_resultados');
    }
};
