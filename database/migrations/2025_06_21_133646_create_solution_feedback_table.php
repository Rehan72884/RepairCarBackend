<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('solution_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solution_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('liked')->nullable(); // true=like, false=dislike
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solution_feedback');
    }
};
