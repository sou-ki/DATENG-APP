<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'identity_number',
        'institution',
        'phone_number',
    ];

    // Relationship dengan VisitRequest
    public function visitRequests()
    {
        return $this->hasMany(VisitRequest::class);
    }

    // Get recent visits
    public function recentVisits($limit = 5)
    {
        return $this->visitRequests()
            ->with('division')
            ->orderBy('visit_date', 'desc')
            ->limit($limit)
            ->get();
    }

    // Check if visitor has active visit today
    public function hasActiveVisitToday()
    {
        return $this->visitRequests()
            ->whereDate('visit_date', today())
            ->whereIn('status', ['registered', 'checked_in'])
            ->exists();
    }
}