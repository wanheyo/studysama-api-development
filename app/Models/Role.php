<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $timestamps = false;
    
    protected $fillable = ['name'];

    public function userCourses()
    {
        return $this->hasMany(UserCourse::class);
    }
}
