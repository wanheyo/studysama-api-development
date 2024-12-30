<?php

namespace App\Models;

use App\Models\ResourceFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ResourceFile extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id';
    
    protected $fillable = [
        'name',
        'type',
        'total_download',
        'status'
    ];

    public function resource_files()
    {
        return $this->hasMany(ResourceFile::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
