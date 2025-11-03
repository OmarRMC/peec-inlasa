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
        Schema::create('formularios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_ensayo');
            $table->string('color_primario', 10)->default('#ffffff');
            $table->string('color_secundario', 10)->default('#000000');
            $table->boolean('editable_por_encargado')->default(false);
            $table->boolean('estado')->default(true);
            $table->string('nombre', 255);
            $table->string('codigo', 80)->nullable();
            $table->text('nota')->nullable();
            $table->foreign('id_ensayo')->references('id')->on('ensayo_aptitud')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formularios');
    }
};
