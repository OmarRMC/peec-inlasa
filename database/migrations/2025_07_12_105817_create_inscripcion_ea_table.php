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
        Schema::create('inscripcion_ea', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_inscripcion');
            $table->unsignedBigInteger('id_ea');
            $table->string('descripcion_ea', 100);
            // $table->primary(['id_inscripcion', 'id_ea']);
            $table->foreign('id_inscripcion')->references('id')->on('inscripcion');
            $table->foreign('id_ea')->references('id')->on('ensayo_aptitud');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscripcion_ea');
    }
};
