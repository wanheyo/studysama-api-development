<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCourse extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    
    protected $table = 'user_courses';
    
    public $incrementing = true;
    
    protected $fillable = [
        'user_id',
        'course_id',
        'role_id',
        'rating',
        'comment',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
