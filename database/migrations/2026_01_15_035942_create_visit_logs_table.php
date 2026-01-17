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
        Schema::create('visit_logs', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('visit_request_id')
                ->constrained('visit_requests')
                ->cascadeOnDelete();
                
            $table->enum('action', ['check_in', 'check_out', 'reject']);
            
            $table->foreignId('performed_by')
                ->constrained('users')
                ->cascadeOnDelete();
                
            $table->timestamp('timestamp'); // Ini yang kita gunakan, bukan created_at
            $table->text('notes')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_logs');
    }
};
