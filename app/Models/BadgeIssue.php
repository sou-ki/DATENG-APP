<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BadgeIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'badge_id',
        'issue_type',
        'description',
        'reported_by',
        'reported_at',
        'status',
        'resolution',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    // Relationship dengan Badge
    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

    // Relationship dengan User (reporter)
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by', 'id');
    }

    // Helper untuk issue type label
    public function getIssueTypeLabelAttribute()
    {
        return match($this->issue_type) {
            'lost' => 'Hilang',
            'damaged' => 'Rusak',
            'other' => 'Lainnya',
            default => $this->issue_type,
        };
    }

    // Helper untuk status label
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'reported' => 'Dilaporkan',
            'investigating' => 'Diselidiki',
            'resolved' => 'Selesai',
            'closed' => 'Ditutup',
            default => $this->status,
        };
    }

    // Scope untuk issue yang belum resolved
    public function scopePending($query)
    {
        return $query->whereIn('status', ['reported', 'investigating']);
    }
}