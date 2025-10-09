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
        Schema::create('petition_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('blog_posts')->onDelete('cascade');
            $table->string('nome');
            $table->string('email')->nullable();
            $table->string('tel_whatsapp');
            $table->string('estado');
            $table->string('cidade');
            $table->string('link_facebook')->nullable();
            $table->string('link_instagram')->nullable();
            $table->text('observacao')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('signed_at');
            $table->timestamps();
            
            // Índices para melhor performance
            $table->index('post_id');
            $table->index('estado');
            $table->index('signed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petition_signatures');
    }
};
