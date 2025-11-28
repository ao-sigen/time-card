<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StampCorrection extends Model
{
    use HasFactory;

    protected $table = 'attendance_correction_requests';

    protected $fillable = [
        'attendance_id',
        'user_id',
        'requested_clock_in',
        'requested_clock_out',
        'requested_break_start1',
        'requested_break_end1',
        'requested_break_start2',
        'requested_break_end2',
        'reason',
        'status',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function attendance()
    {
        return $this->belongsTo(Attendance::class, 'attendance_id');
    }
}
