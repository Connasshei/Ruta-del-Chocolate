<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parada_id')->nullable()->constrained('paradas')->nullOnDelete();
            $table->string('ruta_archivo');
            $table->enum('tipo', ['actividad', 'libre'])->default('libre');
            $table->timestamps();

            $table->index(['evento_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fotos');
    }
};
