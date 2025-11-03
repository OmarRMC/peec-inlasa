<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formulario_ensayos_resultados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_formulario');
            $table->unsignedBigInteger('id_laboratorio');
            $table->unsignedBigInteger('id_ciclo');
            $table->date('fecha_inicio_envio_resultados')->nullable();
            $table->date('fecha_fin_envio_resultados')->nullable();
            $table->text('observaciones')->nullable();
            $table->tinyInteger('estado')->unsigned();
            $table->string('gestion', 6);
            $table->string('cod_lab', 20)->nullable();
            $table->string('nombre_lab', 100);
            $table->string('departamento');
            $table->date('fecha_envio')->nullable();
            $table->json('estructura')->nullable();
            $table->timestamps();

            $table->foreign('id_formulario')
                ->references('id')->on('formularios')->onDelete('cascade');
            $table->foreign('id_laboratorio')
                ->references('id')->on('laboratorio')->onDelete('cascade');
            $table->foreign('id_ciclo')
                ->references('id')->on('ciclos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formulario_ensayos_resultados');
    }
};
