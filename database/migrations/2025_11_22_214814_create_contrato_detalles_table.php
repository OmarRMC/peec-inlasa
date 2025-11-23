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
        Schema::create('contrato_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_contrato');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->integer('posicion')->default(1);
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->foreign('id_contrato')
                ->references('id')->on('contratos')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrato_detalles');
    }
};
