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
        Schema::create('palestras', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('speaker'); // Palestrante
            $table->string('speaker_bio')->nullable(); // Bio do palestrante
            $table->string('speaker_photo')->nullable(); // Foto do palestrante
            $table->datetime('event_date'); // Data do evento
            $table->string('location')->nullable(); // Local
            $table->string('duration')->nullable(); // Duração (ex: "2h")
            $table->text('topics')->nullable(); // Tópicos abordados (JSON)
            $table->string('cover_image')->nullable(); // Imagem de capa
            $table->string('video_url')->nullable(); // URL do vídeo (se disponível)
            $table->string('slides_url')->nullable(); // URL dos slides
            $table->integer('max_participants')->nullable(); // Máximo de participantes
            $table->integer('registered_participants')->default(0); // Inscritos
            $table->decimal('price', 8, 2)->default(0); // Preço (0 = gratuito)
            $table->boolean('is_active')->default(true);
            $table->boolean('registration_open')->default(true);
            $table->string('status')->default('scheduled'); // scheduled, ongoing, completed, cancelled
            $table->integer('priority')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'event_date']);
            $table->index(['status', 'registration_open']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('palestras');
    }
};
