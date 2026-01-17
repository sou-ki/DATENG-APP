<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_request_id',
        'action',
        'performed_by',
        'timestamp',
        'notes',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    // Relationship dengan VisitRequest
    public function visitRequest()
    {
        return $this->belongsTo(VisitRequest::class);
    }

    // Relationship dengan User (performer)
    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    // Helper untuk action label
    public function getActionLabelAttribute()
    {
        return match($this->action) {
            'check_in' => 'Check-in',
            'check_out' => 'Check-out',
            'reject' => 'Ditolak',
            default => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }
}