<?php

namespace App\Models;

use App\Models\Topic;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Course extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id';
    
    protected $fillable = [
        'name',
        'desc',
        'total_visit',
        'total_joined',
        'image',
        'average_rating',
        'status'
    ];

    // Add type casting
    protected $casts = [
        'average_rating ' => 'float', // Ensures the rating is returned as a float
        'total_visit' => 'integer',
        'total_joined' => 'integer',
        'status' => 'integer',
    ];

    public function topics()
    {
        return $this->belongsToMany(Topic::class, 'topic_courses');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_courses')
                    ->withPivot(['role_id', 'rating', 'comment', 'status'])
                    ->withTimestamps();
    }

    public function folders()
    {
        return $this->hasMany(Folder::class);
    }

    public function tutorSlots()
    {
        return $this->hasMany(TutorSlot::class);
    }

}
