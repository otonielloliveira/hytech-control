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
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['active', 'completed', 'cancelled', 'expired'])->default('active');
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->string('payment_transaction_id')->nullable();
            $table->integer('progress_percentage')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // Validade do acesso
            $table->timestamp('certificate_issued_at')->nullable();
            $table->string('certificate_number')->nullable();
            $table->json('quiz_scores')->nullable(); // Pontuações dos quizzes
            $table->decimal('final_score', 5, 2)->nullable(); // Nota final
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['client_id', 'course_id']);
            $table->index(['status', 'progress_percentage']);
            $table->index('certificate_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
    }
};
