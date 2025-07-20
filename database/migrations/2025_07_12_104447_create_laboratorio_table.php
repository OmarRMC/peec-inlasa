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
        Schema::create('laboratorio', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario');
            $table->string('cod_lab', 20);
            $table->string('antcod_peec', 10)->nullable();
            $table->string('numsedes_lab', 15)->nullable();
            $table->bigInteger('nit_lab')->nullable();
            $table->string('nombre_lab', 100);
            $table->string('sigla_lab', 20)->nullable();
            $table->unsignedBigInteger('id_nivel');
            $table->unsignedBigInteger('id_tipo');
            $table->unsignedBigInteger('id_categoria');
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
            $table->boolean('status');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();
            $table->foreign('id_tipo')->references('id')->on('tipo_laboratorio');
            $table->foreign('id_categoria')->references('id')->on('categoria');
            $table->foreign('id_nivel')->references('id')->on('nivel_laboratorio');
            $table->foreign('id_municipio')->references('id')->on('municipio');
            $table->foreign('id_pais')->references('id')->on('pais');
            $table->foreign('id_dep')->references('id')->on('departamento');
            $table->foreign('id_prov')->references('id')->on('provincia');
            $table->foreign('id_usuario')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratorio');
    }
};
