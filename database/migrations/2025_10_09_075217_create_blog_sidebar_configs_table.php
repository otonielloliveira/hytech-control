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
        Schema::create('blog_sidebar_configs', function (Blueprint $table) {
            $table->id();
            $table->string('widget_name')->unique(); // notices, polls, tags, youtube, etc.
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('title_color')->default('#1e40af'); // cor do título
            $table->string('background_color')->default('#f8fafc'); // cor de fundo
            $table->string('text_color')->default('#1f2937'); // cor do texto
            $table->text('custom_css')->nullable(); // CSS personalizado
            $table->json('widget_settings')->nullable(); // configurações específicas do widget
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_sidebar_configs');
    }
};
