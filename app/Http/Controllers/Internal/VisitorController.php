<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use App\Models\VisitRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VisitorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        
        $query = Visitor::withCount(['visitRequests']);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('identity_number', 'like', "%{$search}%")
                  ->orWhere('institution', 'like', "%{$search}%");
            });
        }
        
        $visitors = $query->orderBy('full_name')->paginate(20);
        
        return view('internal.visitors.index', compact('visitors', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('internal.visitors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'identity_number' => ['required', 'string', 'max:20', 'unique:visitors'],
            'institution' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);
        
        Visitor::create($validated);
        
        return redirect()->route('internal.visitors.index')
            ->with('success', 'Visitor berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Visitor $visitor)
    {
        $visitor->loadCount('visitRequests');
        
        $visitStats = $visitor->visitRequests()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
            
        $recentVisits = $visitor->visitRequests()
            ->with(['division', 'creator'])
            ->orderBy('visit_date', 'desc')
            ->limit(10)
            ->get();
            
        return view('internal.visitors.show', compact(
            'visitor', 
            'visitStats',
            'recentVisits'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Visitor $visitor)
    {
        return view('internal.visitors.edit', compact('visitor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Visitor $visitor)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'identity_number' => ['required', 'string', 'max:20', 
                Rule::unique('visitors')->ignore($visitor->id)],
            'institution' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);
        
        $visitor->update($validated);
        
        return redirect()->route('internal.visitors.show', $visitor)
            ->with('success', 'Data visitor berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Visitor $visitor)
    {
        // Check if visitor has visit requests
        if ($visitor->visitRequests()->exists()) {
            return redirect()->route('internal.visitors.index')
                ->with('error', 'Tidak dapat menghapus visitor yang memiliki riwayat kunjungan.');
        }
        
        $visitor->delete();
        
        return redirect()->route('internal.visitors.index')
            ->with('success', 'Visitor berhasil dihapus.');
    }
}