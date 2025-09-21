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
        Schema::create('opciones_selector', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_grupo_selector');
            $table->unsignedBigInteger('id_grupo_hijo')->nullable();
            $table->string('valor', 255);
            $table->string('etiqueta', 255)->nullable();
            $table->integer('posicion')->default(0);
            $table->foreign('id_grupo_selector')
                ->references('id')
                ->on('grupos_selectores')
                ->onDelete('cascade');

            $table->foreign('id_grupo_hijo')
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
        Schema::dropIfExists('opciones_selector');
    }
};
