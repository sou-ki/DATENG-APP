<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'badge_code',
        'access_area',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Relationship dengan BadgeAssignment
    public function badgeAssignments()
    {
        return $this->hasMany(BadgeAssignment::class);
    }

    // Get current assignment (if any)
    public function currentAssignment()
    {
        return $this->badgeAssignments()
            ->whereNull('returned_at')
            ->first();
    }

    // Check if badge is available
    public function isAvailable()
    {
        return $this->status === 'available';
    }

    // Scope for available badges
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    // Scope for in-use badges
    public function scopeInUse($query)
    {
        return $query->where('status', 'in_use');
    }
}