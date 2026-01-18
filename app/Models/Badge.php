<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function scopeWithIssues($query)
    {
        return $query->where('status', 'in_use')
            ->whereDoesntHave('badgeAssignments', function($q) {
                $q->whereNull('returned_at');
            });
    }

    public function hasActiveIssue()
    {
        return DB::table('visit_logs')
            ->whereNull('visit_request_id')
            ->where('action', 'badge_issue')
            ->where('notes', 'like', "%Badge {$this->badge_code}%")
            ->exists();
    }
}