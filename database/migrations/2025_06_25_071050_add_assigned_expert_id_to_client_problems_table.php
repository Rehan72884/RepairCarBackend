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
        Schema::table('client_problems', function (Blueprint $table) {
            $table->foreignId('assigned_expert_id')->nullable()->after('status')->constrained('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('client_problems', function (Blueprint $table) {
            $table->dropForeign(['assigned_expert_id']);
            $table->dropColumn('assigned_expert_id');
        });
    }
};
