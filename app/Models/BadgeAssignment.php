<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BadgeAssignment extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'visit_request_id',
        'badge_id',
        'assigned_by',
        'assigned_at',
        'returned_at',
    ];

    public function visitRequest()
    {
        return $this->belongsTo(VisitRequest::class);
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
