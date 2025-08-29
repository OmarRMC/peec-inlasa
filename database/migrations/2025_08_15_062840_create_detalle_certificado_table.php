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
        Schema::create('detalle_certificado', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_certificado');
            $table->unsignedBigInteger('id_paquete')->nullable();
            $table->unsignedBigInteger('id_ea');
            $table->string('detalle_area');
            $table->string('detalle_ea', 400);
            $table->string('calificacion_certificado', 100)->nullable();
            $table->boolean('temporal')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('id_certificado')->references('id')->on('certificado')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_certificado');
    }
};
