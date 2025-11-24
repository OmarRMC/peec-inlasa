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
        Schema::create('ensayo_formulario', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('formulario_id');
            $table->unsignedBigInteger('ensayo_id');
            $table->foreign('formulario_id')->references('id')->on('formularios')->onDelete('cascade');
            $table->foreign('ensayo_id')->references('id')->on('ensayo_aptitud')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ensayo_formulario');
    }
};
