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
        Schema::create('provincia', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_dep');
            $table->string('nombre_prov', 50);
            $table->integer('cod_prov');
            $table->boolean('status_prov');
            $table->foreign('id_dep')->references('id')->on('departamento');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provincia');
    }
};
