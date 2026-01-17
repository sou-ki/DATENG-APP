<?php

// app/Http/Controllers/InternalDashboardController.php
namespace App\Http\Controllers;

use App\Models\VisitRequest;
use Illuminate\Support\Facades\Auth;

class InternalDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $counts = [
            'registered' => VisitRequest::where('created_by', $user->id)
                ->where('status', 'registered')
                ->count(),
            'checked_in' => VisitRequest::where('created_by', $user->id)
                ->where('status', 'checked_in')
                ->count(),
            'checked_out' => VisitRequest::where('created_by', $user->id)
                ->where('status', 'checked_out')
                ->whereDate('updated_at', '>=', now()->subWeek())
                ->count(),
        ];
        
        $recentVisits = VisitRequest::with('visitor')
            ->where('created_by', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        return view('internal.dashboard', compact('counts', 'recentVisits'));
    }
}

