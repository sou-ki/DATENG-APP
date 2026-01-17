<?php

// app/Http/Controllers/SecurityDashboardController.php
namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\VisitRequest;

class SecurityDashboardController extends Controller
{
    public function index()
    {
        $todayCounts = [
            'pending' => VisitRequest::where('status', 'registered')
                ->where('visit_date', today())
                ->count(),
            'active' => VisitRequest::where('status', 'checked_in')
                ->where('visit_date', today())
                ->count(),
            'completed' => VisitRequest::where('status', 'checked_out')
                ->where('visit_date', today())
                ->count(),
        ];
        
        $availableBadges = Badge::where('status', 'available')->count();
        
        return view('security.dashboard', compact('todayCounts', 'availableBadges'));
    }
}
