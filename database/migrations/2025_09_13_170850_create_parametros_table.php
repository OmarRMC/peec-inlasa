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
            $table->string('nombre', 255);
            $table->boolean('visible_nombre')->default(true);
            $table->boolean('requerido_si_completa')->default(true);
            $table->foreign('id_seccion')
                ->references('id')
                ->on('secciones')
                ->onDelete('cascade');
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
