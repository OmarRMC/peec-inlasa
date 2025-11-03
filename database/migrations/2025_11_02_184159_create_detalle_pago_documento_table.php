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
        Schema::create('detalle_pago_documento', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('documento_inscripcion_id');
            $table->bigInteger('nit')->nullable();
            $table->string('razon_social', 150)->nullable();
            $table->foreign('documento_inscripcion_id')
                ->references('id')
                ->on('documento_inscripcion')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_pago_documento');
    }
};
