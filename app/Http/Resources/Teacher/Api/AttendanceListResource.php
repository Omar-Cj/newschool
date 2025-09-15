<?php

namespace App\Http\Resources\Teacher\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
       // return parent::toArray($request);

        return [
            'student_name' => @$this->student->full_name,
            'student_id' => $this->student_id,
            'attendence_status' => $this->attendanceStatus($this->attendance),
            'attendance_note' => $this->note
        ];
    }


    private function attendanceStatus($attendance){
        $attendanceStatus =
        $attendance == 1 ? 'Present' : (
        $attendance == 2 ? 'Late' : (
        $attendance == 3 ? 'Absent' : (
        $attendance == 4 ? 'Half Day' : '')));

        return $attendanceStatus;
    }
}
