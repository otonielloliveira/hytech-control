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
        Schema::table('blog_polls', function (Blueprint $table) {
            $table->integer('priority')->default(1)->after('is_active');
            $table->datetime('expires_at')->nullable()->after('priority');
            $table->dropColumn(['allow_multiple_votes', 'start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_polls', function (Blueprint $table) {
            $table->dropColumn(['priority', 'expires_at']);
            $table->boolean('allow_multiple_votes')->default(false);
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
        });
    }
};
