<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\User;
use App\Models\Visitor;
use App\Models\VisitRequest;
use App\Models\Division; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    /**
     * Display admin dashboard with all management features
     */
    public function index(Request $request)
    {
        // Statistics
        $stats = [
            'users' => [
                'total' => User::count(),
                'internal' => User::where('role', 'internal')->count(),
                'security' => User::where('role', 'security')->count(),
                'admin' => User::where('role', 'admin')->count(),
            ],
            'visitors' => Visitor::count(),
            'badges' => [
                'total' => Badge::count(),
                'available' => Badge::where('status', 'available')->count(),
                'in_use' => Badge::where('status', 'in_use')->count(),
            ],
            'visits_today' => VisitRequest::whereDate('visit_date', today())->count(),
        ];

        // Get data for tables
        $users = User::with('division')->orderBy('name')->get();
        $badges = Badge::orderBy('badge_code')->get();
        $visitors = Visitor::withCount('visitRequests')->orderBy('full_name')->get();
        $divisions = Division::withCount(['users', 'visitRequests'])->orderBy('division_name')->get();

        return view('admin.dashboard', compact('stats', 'users', 'badges', 'visitors', 'divisions'));
    }

    /**
     * Create new user
     */
    public function createUser(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:internal,security,admin'],
            'division_id' => ['nullable', 'required_if:role,internal', 'exists:divisions,id'],
        ]);

        $userData = $request->only(['name', 'email', 'role', 'division_id']);
        $userData['password'] = Hash::make($request->password);

        User::create($userData);

        return redirect()->route('admin.dashboard')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 
                Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'in:internal,security,admin'],
            'division_id' => ['nullable', 'required_if:role,internal', 'exists:divisions,id'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $userData = $request->only(['name', 'email', 'role', 'division_id']);
        
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        // Reset division if not internal
        if ($request->role !== 'internal') {
            $userData['division_id'] = null;
        }

        $user->update($userData);

        return redirect()->route('admin.dashboard')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.dashboard')->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        if ($user->visitRequests()->exists()) {
            return redirect()->route('admin.dashboard')->with('error', 'User memiliki riwayat kunjungan.');
        }

        $user->delete();

        return redirect()->route('admin.dashboard')->with('success', 'User berhasil dihapus.');
    }

    /**
     * Create new division
     */
    public function createDivision(Request $request)
    {
        $request->validate([
            'division_name' => ['required', 'string', 'max:255', 'unique:divisions'],
            'description' => ['nullable', 'string'],
        ]);

        Division::create($request->only(['division_name', 'description']));

        return redirect()->route('admin.dashboard')->with('success', 'Divisi berhasil ditambahkan.');
    }

    /**
     * Update division
     */
    public function updateDivision(Request $request, Division $division)
    {
        $request->validate([
            'division_name' => ['required', 'string', 'max:255', 
                Rule::unique('divisions')->ignore($division->id)],
            'description' => ['nullable', 'string'],
        ]);

        $division->update($request->only(['division_name', 'description']));

        return redirect()->route('admin.dashboard')->with('success', 'Divisi berhasil diperbarui.');
    }

    /**
     * Delete division
     */
    public function deleteDivision(Division $division)
    {
        if ($division->users()->exists()) {
            return redirect()->route('admin.dashboard')->with('error', 'Divisi memiliki user terkait.');
        }

        if ($division->visitRequests()->exists()) {
            return redirect()->route('admin.dashboard')->with('error', 'Divisi memiliki riwayat kunjungan.');
        }

        $division->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Divisi berhasil dihapus.');
    }

    /**
     * Update visitor
     */
    public function updateVisitor(Request $request, Visitor $visitor)
    {
        $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'identity_number' => ['required', 'string', 'max:50', 
                Rule::unique('visitors')->ignore($visitor->id)],
            'institution' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
        ]);

        $visitor->update($request->only(['full_name', 'identity_number', 'institution', 'phone_number']));

        return redirect()->route('admin.dashboard')->with('success', 'Visitor berhasil diperbarui.');
    }

    /**
     * Create new badge
     */
    public function createBadge(Request $request)
    {
        $request->validate([
            'badge_code' => ['required', 'string', 'max:50', 'unique:badges'],
            'access_area' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:available,in_use'],
        ]);

        Badge::create($request->only(['badge_code', 'access_area', 'status']));

        return redirect()->route('admin.dashboard')->with('success', 'Badge berhasil ditambahkan.');
    }

    /**
     * Update badge
     */
    public function updateBadge(Request $request, Badge $badge)
    {
        $request->validate([
            'badge_code' => ['required', 'string', 'max:50', 
                Rule::unique('badges')->ignore($badge->id)],
            'access_area' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:available,in_use'],
        ]);

        $badge->update($request->only(['badge_code', 'access_area', 'status']));

        return redirect()->route('admin.dashboard')->with('success', 'Badge berhasil diperbarui.');
    }

    /**
     * Delete badge
     */
    public function deleteBadge(Badge $badge)
    {
        if ($badge->badgeAssignments()->exists()) {
            return redirect()->route('admin.dashboard')->with('error', 'Badge memiliki riwayat penggunaan.');
        }

        $badge->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Badge berhasil dihapus.');
    }

    /**
     * Delete visitor
     */
    public function deleteVisitor(Visitor $visitor)
    {
        if ($visitor->visitRequests()->exists()) {
            return redirect()->route('admin.dashboard')->with('error', 'Visitor memiliki riwayat kunjungan.');
        }

        $visitor->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Visitor berhasil dihapus.');
    }
}