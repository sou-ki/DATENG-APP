<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Division extends Model
{
    use HasFactory;

    protected $fillable = [
        'division_name',
        'description',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function visitRequests()
    {
        return $this->hasMany(VisitRequest::class);
    }
}
