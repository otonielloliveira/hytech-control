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
        Schema::table('blog_configs', function (Blueprint $table) {
            // YouTube integration
            $table->string('youtube_channel_url')->nullable();
            $table->string('youtube_channel_name')->nullable();
            $table->boolean('show_youtube_widget')->default(false);
            
            // Sidebar global settings
            $table->boolean('show_sidebar')->default(true);
            $table->string('sidebar_position')->default('right'); // right, left
            $table->string('sidebar_width')->default('300px');
            $table->string('default_widget_title_color')->default('#1e40af');
            $table->string('default_widget_background_color')->default('#f8fafc');
            $table->string('default_widget_text_color')->default('#1f2937');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_configs', function (Blueprint $table) {
            $table->dropColumn([
                'youtube_channel_url',
                'youtube_channel_name', 
                'show_youtube_widget',
                'show_sidebar',
                'sidebar_position',
                'sidebar_width',
                'default_widget_title_color',
                'default_widget_background_color',
                'default_widget_text_color',
            ]);
        });
    }
};
