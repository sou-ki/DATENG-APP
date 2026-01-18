<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $role = $request->query('role');
        
        $query = User::with('division');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($role && in_array($role, ['internal', 'security', 'admin'])) {
            $query->where('role', $role);
        }
        
        $users = $query->orderBy('name')->paginate(20);
        
        return view('admin.users.index', compact('users', 'search', 'role'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $divisions = Division::orderBy('division_name')->get();
        return view('admin.users.create', compact('divisions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:internal,security,admin'],
            'division_id' => ['nullable', 'required_if:role,internal', 'exists:divisions,id'],
        ]);

        // Handle division based on role
        if ($validated['role'] !== 'internal') {
            $validated['division_id'] = null;
        }

        $validated['password'] = Hash::make($validated['password']);
        
        User::create($validated);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['division', 'visitRequests' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }]);
        
        $visitStats = $user->visitRequests()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
            
        return view('admin.users.show', compact('user', 'visitStats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $divisions = Division::orderBy('division_name')->get();
        return view('admin.users.edit', compact('user', 'divisions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 
                Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:internal,security,admin'],
            'division_id' => ['nullable', 'required_if:role,internal', 'exists:divisions,id'],
        ]);

        // Handle division based on role
        if ($validated['role'] !== 'internal') {
            $validated['division_id'] = null;
        }

        // Update password only if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        
        $user->update($validated);
        
        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Data user berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri.');
        }
        
        // Check if user has created visit requests
        if ($user->visitRequests()->exists()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tidak dapat menghapus user yang telah membuat kunjungan.');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}