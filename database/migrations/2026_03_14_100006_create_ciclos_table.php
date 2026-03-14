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
        Schema::create('ciclos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_ensayo')->nullable();
            $table->string('nombre');
            $table->integer('numero');

            $table->date('fecha_inicio_envio_muestras')->nullable();
            $table->date('fecha_fin_envio_muestras')->nullable();
            $table->date('fecha_inicio_envio_resultados')->nullable();
            $table->date('fecha_fin_envio_resultados')->nullable();
            $table->date('fecha_inicio_envio_reporte')->nullable();
            $table->date('fecha_fin_envio_reporte')->nullable();
            $table->boolean('estado')->default(true);

            $table->foreign('id_ensayo')
                ->references('id')
                ->on('ensayo_aptitud')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ciclos');
    }
};
