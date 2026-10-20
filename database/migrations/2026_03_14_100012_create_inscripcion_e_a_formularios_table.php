<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscripcion_ea_formulario', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inscripcion_ea_id');
            $table->unsignedBigInteger('formulario_id');
            $table->integer('cantidad')->default(1);
            $table->timestamps();
            $table->foreign('inscripcion_ea_id')
                ->references('id')->on('inscripcion_ea')
                ->onDelete('cascade');
            $table->foreign('formulario_id')
                ->references('id')->on('formularios')
                ->onDelete('cascade');
            $table->unique(['inscripcion_ea_id', 'formulario_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripcion_ea_formulario');
    }
};
