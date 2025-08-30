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
        Schema::create('area', function (Blueprint $table) {
            $table->id();
              $table->unsignedBigInteger('id_programa');
            $table->string('descripcion', 100);
            $table->boolean('status');
            $table->integer('max_paquetes_inscribir')->default(0);
            $table->foreign('id_programa')->references('id')->on('programa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area');
    }
};
