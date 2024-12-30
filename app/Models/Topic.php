<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $primaryKey = 'topic_id';
    public $timestamps = false;
    
    protected $fillable = [
        'name',
        'desc',
        'total_course'
    ];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'topic_courses');
    }
}
