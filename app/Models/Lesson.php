<?php

namespace App\Models;

use App\Models\Course;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Lesson extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id';
    
    protected $fillable = [
        'course_id',
        'name',
        'learn_outcome',
        'desc',
        'total_visit',
        'status'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }
}
