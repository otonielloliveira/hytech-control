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
        Schema::create('certificate_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome do tipo de certificado
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('template_file')->nullable(); // Arquivo do template do certificado
            $table->json('template_config')->nullable(); // Configurações do template (posições, fontes, etc)
            $table->integer('min_completion_percentage')->default(100); // % mínimo para obter certificado
            $table->boolean('requires_exam')->default(false); // Requer prova final
            $table->decimal('min_exam_score', 5, 2)->nullable(); // Nota mínima na prova
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_types');
    }
};
