<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $primaryKey = 'badge_id';
    public $timestamps = false;
    
    protected $fillable = [
        'name',
        'desc',
        'rarity', //c: common, u: uncommon, r: rare, e: epic, l: legendary.
        'total_user',
        'status'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges')
                    ->withTimestamps();
    }
}
