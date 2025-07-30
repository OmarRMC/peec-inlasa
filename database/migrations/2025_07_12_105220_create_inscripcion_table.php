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
        Schema::create('inscripcion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_lab');
            $table->unsignedBigInteger('id_formulario')->nullable();
            $table->integer('cant_paq');
            $table->integer('costo_total');
            $table->string('obs_inscripcion', 255)->nullable();
            $table->dateTime('fecha_inscripcion');
            $table->boolean('status_cuenta');
            $table->boolean('status_inscripcion');
            $table->date('fecha_limite_pago')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->string('gestion', 10);

            $table->foreign('id_lab')->references('id')->on('laboratorio');
            $table->foreign('id_formulario')->references('id')->on('formulario');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscripcion');
    }
};
