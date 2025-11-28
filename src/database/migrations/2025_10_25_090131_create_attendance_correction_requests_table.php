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
        Schema::create('attendance_correction_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_id'); // 対象の勤怠
            $table->unsignedBigInteger('user_id'); // 申請者
            $table->time('requested_clock_in')->nullable(); // 修正後の出勤時刻
            $table->time('requested_clock_out')->nullable(); // 修正後の退勤時刻
            $table->time('requested_break_start1')->nullable();
            $table->time('requested_break_end1')->nullable();
            $table->time('requested_break_start2')->nullable();
            $table->time('requested_break_end2')->nullable();
            $table->text('reason'); // 修正理由
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->timestamps();

            $table->foreign('attendance_id')->references('id')->on('attendances')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_correction_requests');
    }
};
