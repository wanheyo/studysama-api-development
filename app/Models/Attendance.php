<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $primaryKey = 'attendance_id';
    public $timestamps = false;
    
    protected $fillable = [
        'tutor_slot_id',
        'user_course_id',
        'attendance_status',
        'status'
    ];

    public function tutorSlot()
    {
        return $this->belongsTo(TutorSlot::class);
    }

    public function userCourse()
    {
        return $this->belongsTo(UserCourse::class);
    }

}
