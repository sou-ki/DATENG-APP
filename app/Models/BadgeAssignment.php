<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BadgeAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_request_id',
        'badge_id',
        'assigned_by',
        'assigned_at',
        'returned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public $timestamps = false;

    // Relationship dengan VisitRequest
    public function visitRequest()
    {
        return $this->belongsTo(VisitRequest::class);
    }

    // Relationship dengan Badge
    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

    // Relationship dengan User (assigned by)
    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}