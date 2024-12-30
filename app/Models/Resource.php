<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Resource extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id';
    
    protected $fillable = [
        'lesson_id',
        'file_id',
        'name',
        'desc',
        'link',
        'category',
        'total_visit',
        'status'
    ];

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
