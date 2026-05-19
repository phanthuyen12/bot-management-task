<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('telegram_user_id')->constrained('telegram_users')->onDelete('cascade');
            $table->string('type'); // daily, weekly, monthly
            $table->date('date');
            $table->json('data'); // Answers to questions
            $table->integer('points_earned')->default(0);
            $table->string('status')->default('submitted'); // submitted, late
            $table->timestamps();
            
            $table->unique(['telegram_user_id', 'type', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
