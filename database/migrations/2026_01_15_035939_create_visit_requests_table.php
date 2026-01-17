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
        Schema::create('visit_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('visitor_id')
                ->constrained('visitors')
                ->cascadeOnDelete();

            $table->foreignId('division_id')
                ->constrained('divisions')
                ->cascadeOnDelete();

            $table->text('purpose');

            $table->enum('visit_type', [
                'antar_barang',
                'ambil_barang',
                'kunjungan',
                'inspeksi',
                'lainnya'
            ]);

            $table->date('visit_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();

            $table->string('letter_path')->nullable();

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('status', [
                'registered',
                'checked_in',
                'checked_out',
                'rejected'
            ])->default('registered');

            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
