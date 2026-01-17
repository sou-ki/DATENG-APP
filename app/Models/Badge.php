<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'badge_code',
        'access_area',
        'status',
    ];

    public function assignments()
    {
        return $this->hasMany(BadgeAssignment::class);
    }
}
