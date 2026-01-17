<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'identity_number',
        'institution',
        'phone_number',
    ];

    public function visitRequests()
    {
        return $this->hasMany(VisitRequest::class);
    }
}
