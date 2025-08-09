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
        Schema::create('tipo_laboratorio_programa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_programa');
            $table->unsignedBigInteger('id_tipo');

            // $table->primary(['id_programa', 'id_tipo']);

            $table->foreign('id_programa')->references('id')->on('programa');
            $table->foreign('id_tipo')->references('id')->on('tipo_laboratorio');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_laboratorio_programa');
    }
};
