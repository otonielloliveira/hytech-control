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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Título do curso
            $table->string('slug')->unique();
            $table->text('description'); // Descrição completa
            $table->text('short_description')->nullable(); // Descrição curta
            $table->string('image')->nullable(); // Imagem de capa
            $table->string('trailer_video')->nullable(); // Vídeo de apresentação
            $table->foreignId('certificate_type_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->decimal('price', 10, 2)->default(0); // Preço do curso
            $table->decimal('promotional_price', 10, 2)->nullable(); // Preço promocional
            $table->timestamp('promotion_ends_at')->nullable();
            $table->integer('estimated_hours')->nullable(); // Horas estimadas
            $table->integer('max_enrollments')->nullable(); // Máximo de matrículas
            $table->json('requirements')->nullable(); // Pré-requisitos
            $table->json('what_you_will_learn')->nullable(); // O que vai aprender
            $table->json('target_audience')->nullable(); // Público-alvo
            $table->text('instructor_notes')->nullable(); // Notas do instrutor
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'published_at']);
            $table->index(['is_featured', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
