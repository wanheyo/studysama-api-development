<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    
    protected $fillable = [
        'user_course_id',
        'resource_id',
        'comment_text',
        'status',
        'file_id'
    ];

    public function userCourse()
    {
        return $this->belongsTo(UserCourse::class);
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
