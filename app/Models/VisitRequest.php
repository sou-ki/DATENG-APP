<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_id',
        'division_id',
        'purpose',
        'visit_type',
        'visit_date',
        'start_time',
        'end_time',
        'letter_path',
        'status',
        'created_by',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    // Relationship dengan Visitor
    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }

    // Relationship dengan Division
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    // Relationship dengan User (creator)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship dengan BadgeAssignment
    public function badgeAssignment()
    {
        return $this->hasOne(BadgeAssignment::class);
    }

    // Relationship dengan VisitLog
    public function visitLogs()
    {
        return $this->hasMany(VisitLog::class);
    }

    // Helper method untuk mendapatkan visit_type dalam format yang lebih readable
    public function getVisitTypeLabelAttribute()
    {
        return match($this->visit_type) {
            'antar_barang' => 'Antar Barang',
            'ambil_barang' => 'Ambil Barang',
            'kunjungan' => 'Kunjungan',
            'inspeksi' => 'Inspeksi',
            'lainnya' => 'Lainnya',
            default => $this->visit_type,
        };
    }

    // Helper method untuk mendapatkan status dalam format yang lebih readable
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'registered' => 'Terdaftar',
            'checked_in' => 'Check-in',
            'checked_out' => 'Check-out',
            'rejected' => 'Ditolak',
            default => $this->status,
        };
    }

    // Scope untuk filtering
    public function scopeToday($query)
    {
        return $query->whereDate('visit_date', today());
    }

    public function scopeRegistered($query)
    {
        return $query->where('status', 'registered');
    }

    public function scopeCheckedIn($query)
    {
        return $query->where('status', 'checked_in');
    }

    public function scopeCheckedOut($query)
    {
        return $query->where('status', 'checked_out');
    }
}