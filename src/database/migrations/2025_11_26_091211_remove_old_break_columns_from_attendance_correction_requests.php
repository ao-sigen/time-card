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
            if (Schema::hasColumn('attendance_correction_requests', 'requested_break_start')) {
                $table->dropColumn('requested_break_start');
            }
            if (Schema::hasColumn('attendance_correction_requests', 'requested_break_end')) {
                $table->dropColumn('requested_break_end');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_correction_requests', function (Blueprint $table) {
            $table->time('requested_break_start')->nullable();
            $table->time('requested_break_end')->nullable();
        });
    }
};
