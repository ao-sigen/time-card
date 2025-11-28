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
        Schema::table('attendance_correction_requests', function (Blueprint $table) {
            $table->time('requested_break_start1')->nullable();
            $table->time('requested_break_end1')->nullable();
            $table->time('requested_break_start2')->nullable();
            $table->time('requested_break_end2')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_correction_requests', function (Blueprint $table) {
            'requested_break_start1';
            'requested_break_end1';
            'requested_break_start2';
            'requested_break_end2';
        });
    }
};
