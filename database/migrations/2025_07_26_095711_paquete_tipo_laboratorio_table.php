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
        Schema::create('paquete_tipo_laboratorio', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paquete_id');
            $table->unsignedBigInteger('tipo_laboratorio_id');
            $table->timestamps();
            $table->foreign('paquete_id')->references('id')->on('paquete')->onDelete('cascade');
            $table->foreign('tipo_laboratorio_id')->references('id')->on('tipo_laboratorio')->onDelete('cascade');
            $table->unique(['paquete_id', 'tipo_laboratorio_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paquete_tipo_laboratorio');
    }
};
