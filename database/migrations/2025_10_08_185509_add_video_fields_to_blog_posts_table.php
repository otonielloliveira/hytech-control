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
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->enum('video_type', ['none', 'youtube', 'vimeo', 'custom'])->default('none')->after('featured_image');
            $table->string('video_url')->nullable()->after('video_type');
            $table->text('video_embed_code')->nullable()->after('video_url');
            $table->boolean('show_video_in_content')->default(false)->after('video_embed_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn(['video_type', 'video_url', 'video_embed_code', 'show_video_in_content']);
        });
    }
};
