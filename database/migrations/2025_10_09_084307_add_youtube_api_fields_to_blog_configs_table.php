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
            $table->string('youtube_api_key')->nullable()->after('youtube_channel_name');
            $table->string('youtube_channel_id')->nullable()->after('youtube_api_key');
            $table->json('youtube_channel_data')->nullable()->after('youtube_channel_id');
            $table->timestamp('youtube_data_last_update')->nullable()->after('youtube_channel_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_configs', function (Blueprint $table) {
            $table->dropColumn([
                'youtube_api_key',
                'youtube_channel_id',
                'youtube_channel_data',
                'youtube_data_last_update'
            ]);
        });
    }
};
