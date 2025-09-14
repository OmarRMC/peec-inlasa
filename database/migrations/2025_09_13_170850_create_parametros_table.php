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
        Schema::create('parametros', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_seccion');
            $table->unsignedBigInteger('id_grupo_selector')->nullable();
            $table->string('nombre', 255);
            $table->enum('tipo', ['text', 'number', 'date', 'select', 'checkbox', 'textarea']);
            $table->string('unidad', 50)->nullable();
            $table->json('validacion')->nullable();
            $table->boolean('requerido')->default(false);
            $table->integer('posicion')->default(0);
            $table->foreign('id_seccion')
                ->references('id')
                ->on('secciones')
                ->onDelete('cascade');

            $table->foreign('id_grupo_selector')
                ->references('id')
                ->on('grupos_selectores')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parametros');
    }
};
