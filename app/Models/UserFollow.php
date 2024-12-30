<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFollow extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'user_follow';

    // Fillable fields
    protected $fillable = [
        'user_follower_id',
        'user_followed_id',
        'status',
    ];

    // Relationships
    public function follower()
    {
        return $this->belongsTo(User::class, 'user_follower_id');
    }

    public function followed()
    {
        return $this->belongsTo(User::class, 'user_followed_id');
    }
}
