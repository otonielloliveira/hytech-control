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
        Schema::create('section_configs', function (Blueprint $table) {
            $table->id();
            $table->string('section_key')->unique(); // ex: 'featured_posts', 'news_mundial', etc
            $table->string('section_name'); // Nome exibido
            $table->string('section_icon')->nullable(); // Ícone FontAwesome
            $table->string('section_description')->nullable(); // Descrição da seção
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
        Schema::dropIfExists('section_configs');
    }
};
