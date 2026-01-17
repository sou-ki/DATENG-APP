<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'created_by',
        'status',
    ];

    // RELATIONSHIPS

    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function badgeAssignment()
    {
        return $this->hasOne(BadgeAssignment::class);
    }

    public function logs()
    {
        return $this->hasMany(VisitLog::class);
    }
}
