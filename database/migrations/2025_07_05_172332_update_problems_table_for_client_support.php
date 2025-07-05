<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('problems', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'assigned', 'solved'])->default('pending');
            $table->foreignId('assigned_expert_id')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('problems', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['assigned_expert_id']);
            $table->dropColumn(['client_id', 'status', 'assigned_expert_id']);
        });
    }
};
