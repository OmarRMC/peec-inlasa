<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('informe_tecnico', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique()->nullable();
            $table->unsignedBigInteger('id_ciclo');
            $table->unsignedBigInteger('id_laboratorio');
            $table->string('gestion', 5);
            $table->string('reporte')->nullable();
            $table->tinyInteger('estado')->unsigned()->default(0);
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('id_ciclo')->references('id')->on('ciclos');
            $table->foreign('id_laboratorio')->references('id')->on('laboratorio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('informe_tecnico');
    }
};
