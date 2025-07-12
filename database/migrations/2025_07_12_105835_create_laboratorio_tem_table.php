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
        Schema::create('laboratorio_tem', function (Blueprint $table) {
            $table->id();
            $table->string('cod_lab', 20);
            $table->string('antcod_peec', 10)->nullable();
            $table->string('numsedes_lab', 15)->nullable();
            $table->bigInteger('nit_lab');
            $table->string('nombre_lab', 100);
            $table->string('sigla_lab', 20)->nullable();
            $table->unsignedBigInteger('id_nivel');
            $table->unsignedBigInteger('id_tipo');
            $table->string('respo_lab', 50);
            $table->string('ci_respo_lab', 12)->nullable();
            $table->string('repreleg_lab', 50);
            $table->string('ci_repreleg_lab', 12)->nullable();
            $table->unsignedBigInteger('id_pais');
            $table->unsignedBigInteger('id_dep');
            $table->unsignedBigInteger('id_prov');
            $table->unsignedBigInteger('id_municipio');
            $table->string('zona_lab', 50);
            $table->string('direccion_lab', 150);
            $table->integer('wapp_lab');
            $table->integer('wapp2_lab')->nullable();
            $table->string('mail_lab', 50);
            $table->string('mail2_lab', 50)->nullable();
            $table->string('password', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratorio_tem');
    }
};
