<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('respuestas_formulario', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_formulario_resultado')->nullable();
            $table->unsignedBigInteger('id_parametro')->nullable();
            $table->string('nombre');
            $table->boolean('visible_nombre')->default(true);
            $table->json('respuestas');
            $table->timestamps();
            $table->foreign('id_formulario_resultado')
                ->references('id')->on('formulario_ensayos_resultados')->onDelete('set null');
            $table->foreign('id_parametro')
                ->references('id')->on('parametros')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('respuestas_formulario');
    }
};
