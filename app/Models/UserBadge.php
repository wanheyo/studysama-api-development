<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBadge extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    
    protected $table = 'user_badges';
    
    public $incrementing = true;
    
    protected $fillable = [
        'user_id',
        'badge_id',
        'status'
    ];
}
