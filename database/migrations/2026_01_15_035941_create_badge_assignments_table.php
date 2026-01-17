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
        Schema::create('badge_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('visit_request_id')
                ->constrained('visit_requests')
                ->cascadeOnDelete();

            $table->foreignId('badge_id')
                ->constrained('badges')
                ->cascadeOnDelete();

            $table->foreignId('assigned_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamp('assigned_at');
            $table->timestamp('returned_at')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badge_assignments');
    }
};
