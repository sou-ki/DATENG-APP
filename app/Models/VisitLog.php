<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VisitLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'visit_request_id',
        'action',
        'performed_by',
        'timestamp',
        'notes',
    ];

    public function visitRequest()
    {
        return $this->belongsTo(VisitRequest::class);
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
