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
        Schema::create('blog_hangouts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('platform'); // google-meet, zoom, teams, etc
            $table->string('meeting_link');
            $table->string('meeting_id')->nullable();
            $table->string('meeting_password')->nullable();
            $table->datetime('scheduled_at');
            $table->integer('duration_minutes')->default(60);
            $table->integer('max_participants')->nullable();
            $table->string('host_name');
            $table->string('host_email')->nullable();
            $table->text('agenda')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, live, ended, cancelled
            $table->boolean('is_public')->default(true);
            $table->boolean('requires_registration')->default(false);
            $table->string('cover_image')->nullable();
            $table->integer('priority')->default(1);
            $table->timestamps();
            
            $table->index(['status', 'scheduled_at']);
            $table->index(['is_public', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_hangouts');
    }
};
