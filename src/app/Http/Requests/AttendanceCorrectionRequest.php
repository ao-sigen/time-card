<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceCorrectionRequest extends FormRequest
{
    public function authorize()
    {
        return true; // 認可済みとする場合
    }

    public function rules()
    {
        return [
            'attendance_id' => 'required|exists:attendances,id',
            'requested_clock_in' => 'nullable|date_format:H:i',
            'requested_clock_out' => 'nullable|date_format:H:i',
            'requested_break_start1' => 'nullable|date_format:H:i',
            'requested_break_end1' => 'nullable|date_format:H:i',
            'requested_break_start2' => 'nullable|date_format:H:i',
            'requested_break_end2' => 'nullable|date_format:H:i',
            'reason' => 'required|string',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $in  = $this->requested_clock_in;
            $out = $this->requested_clock_out;

            // 1. 出退勤チェック
            if ($in && $out && $in >= $out) {
                $validator->errors()->add('requested_clock_in', '出勤時間もしくは退勤時間が不適切な値です');
            }

            // 2. 休憩1チェック
            $b1_start = $this->requested_break_start1;
            $b1_end   = $this->requested_break_end1;
            if ($b1_start && $in && $b1_start < $in) {
                $validator->errors()->add('requested_break_start1', '休憩時間が不適切な値です');
            }
            if ($b1_end && $out && $b1_end > $out) {
                $validator->errors()->add('requested_break_end1', '休憩時間もしくは退勤時間が不適切な値です');
            }

            // 3. 休憩2チェック
            $b2_start = $this->requested_break_start2;
            $b2_end   = $this->requested_break_end2;
            if ($b2_start && $in && $b2_start < $in) {
                $validator->errors()->add('requested_break_start2', '休憩時間が不適切な値です');
            }
            if ($b2_end && $out && $b2_end > $out) {
                $validator->errors()->add('requested_break_end2', '休憩時間もしくは退勤時間が不適切な値です');
            }

            // 4. 備考チェック
            if (!$this->reason) {
                $validator->errors()->add('reason', '備考を記入してください');
            }
        });
    }
}
