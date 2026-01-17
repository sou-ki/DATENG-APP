<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use App\Models\Division;
use App\Models\VisitRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class VisitRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status');
        
        $query = VisitRequest::with(['visitor', 'division'])
            ->where('created_by', $user->id);
            
        if ($status && in_array($status, ['registered', 'checked_in', 'checked_out', 'rejected'])) {
            $query->where('status', $status);
        }
        
        $visitRequests = $query->orderBy('visit_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('internal.visit-requests.index', compact('visitRequests', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $visitors = Visitor::orderBy('full_name')->get();
        $divisions = Division::orderBy('division_name')->get();
        
        return view('internal.visit-requests.create', compact('visitors', 'divisions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'visitor_id' => ['required', 'exists:visitors,id'],
            'division_id' => ['required', 'exists:divisions,id'],
            'purpose' => ['required', 'string', 'max:500'],
            'visit_type' => ['required', Rule::in(['antar_barang', 'ambil_barang', 'kunjungan', 'inspeksi', 'lainnya'])],
            'visit_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'letter' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);
        
        // Cek apakah visitor sudah memiliki kunjungan pada tanggal yang sama
        $existingVisit = VisitRequest::where('visitor_id', $validated['visitor_id'])
            ->where('visit_date', $validated['visit_date'])
            ->whereIn('status', ['registered', 'checked_in'])
            ->first();
            
        if ($existingVisit) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Visitor sudah memiliki kunjungan terdaftar untuk tanggal ini.');
        }
        
        // Handle file upload jika ada
        $letterPath = null;
        if ($request->hasFile('letter')) {
            $letterPath = $request->file('letter')->store('visit-letters', 'public');
        }
        
        // Create visit request
        $visitRequest = VisitRequest::create([
            'visitor_id' => $validated['visitor_id'],
            'division_id' => $validated['division_id'],
            'purpose' => $validated['purpose'],
            'visit_type' => $validated['visit_type'],
            'visit_date' => $validated['visit_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'letter_path' => $letterPath,
            'status' => 'registered',
            'created_by' => Auth::id(),
        ]);
        
        return redirect()->route('internal.visit-requests.show', $visitRequest)
            ->with('success', 'Kunjungan berhasil dibuat. Visitor dapat datang sesuai jadwal.');
    }

    /**
     * Display the specified resource.
     */
    public function show(VisitRequest $visitRequest)
    {
        // Authorization check
        if ($visitRequest->created_by !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }
        
        $visitRequest->load(['visitor', 'division', 'creator', 'badgeAssignment.badge', 'visitLogs.performer']);
        
        return view('internal.visit-requests.show', compact('visitRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VisitRequest $visitRequest)
    {
        // Only allow editing if status is still registered
        if ($visitRequest->status !== 'registered') {
            return redirect()->route('internal.visit-requests.show', $visitRequest)
                ->with('error', 'Kunjungan yang sudah diproses tidak dapat diubah.');
        }
        
        if ($visitRequest->created_by !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
        
        $visitors = Visitor::orderBy('full_name')->get();
        $divisions = Division::orderBy('division_name')->get();
        
        return view('internal.visit-requests.edit', compact('visitRequest', 'visitors', 'divisions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VisitRequest $visitRequest)
    {
        // Only allow updating if status is still registered
        if ($visitRequest->status !== 'registered') {
            return redirect()->route('internal.visit-requests.show', $visitRequest)
                ->with('error', 'Kunjungan yang sudah diproses tidak dapat diubah.');
        }
        
        if ($visitRequest->created_by !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
        
        $validated = $request->validate([
            'division_id' => ['required', 'exists:divisions,id'],
            'purpose' => ['required', 'string', 'max:500'],
            'visit_type' => ['required', Rule::in(['antar_barang', 'ambil_barang', 'kunjungan', 'inspeksi', 'lainnya'])],
            'visit_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'letter' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);
        
        // Handle file upload jika ada
        if ($request->hasFile('letter')) {
            // Delete old file if exists
            if ($visitRequest->letter_path && Storage::disk('public')->exists($visitRequest->letter_path)) {
                Storage::disk('public')->delete($visitRequest->letter_path);
            }
            
            $letterPath = $request->file('letter')->store('visit-letters', 'public');
            $validated['letter_path'] = $letterPath;
        }
        
        $visitRequest->update($validated);
        
        return redirect()->route('internal.visit-requests.show', $visitRequest)
            ->with('success', 'Kunjungan berhasil diperbarui.');
    }

    /**
     * Cancel a visit request.
     */
    public function cancel(Request $request, VisitRequest $visitRequest)
    {
        if ($visitRequest->created_by !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
        
        if ($visitRequest->status !== 'registered') {
            return redirect()->route('internal.visit-requests.show', $visitRequest)
                ->with('error', 'Hanya kunjungan yang berstatus "Terdaftar" yang dapat dibatalkan.');
        }
        
        $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:255'],
        ]);
        
        $visitRequest->update([
            'status' => 'rejected',
        ]);
        
        // Create visit log for cancellation
        \App\Models\VisitLog::create([
            'visit_request_id' => $visitRequest->id,
            'action' => 'reject',
            'performed_by' => Auth::id(),
            'timestamp' => now(),
            'notes' => 'Dibatalkan oleh pembuat: ' . $request->cancellation_reason,
        ]);
        
        return redirect()->route('internal.visit-requests.index')
            ->with('success', 'Kunjungan berhasil dibatalkan.');
    }
}