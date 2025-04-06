<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apprentices', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique(); // Cédula o TI
            $table->string('full_name'); // Nombre completo
            $table->string('training_center'); // Centro de formación
            $table->string('photo_path'); // Ruta de la foto del estudiante
            $table->date('start_date'); // Fecha de ingreso
            $table->date('end_date'); // Fecha de finalización
            $table->string('program_name'); // Nombre del técnico/tecnólogo
            $table->string('program_code'); // Ficha del programa
            $table->string('blood_type')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apprentices');
    }
};