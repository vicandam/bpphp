<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MembershipType;
use App\Services\UserMetricsService;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    protected $userMetricsService;

    public function __construct(UserMetricsService $userMetricsService)
    {
        // Apply admin middleware to all methods in this controller.
        $this->middleware(['auth', 'admin']);
        $this->userMetricsService = $userMetricsService;
    }

    /**
     * Display a paginated list of all users with search and filter options.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Implement search functionality by name or email.
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('referral_code', 'like', "%{$search}%");
        }

        // Implement filter functionality by membership type.
        if ($request->filled('filter')) {
            $filter = $request->input('filter');
            $query->where('membership_type_id', $filter);
        }

        $users = $query->with('membershipType')->paginate(10);
        $membershipTypes = MembershipType::all(); // For the filter dropdown

        return view('admin.users.index', compact('users', 'membershipTypes'));
    }

    /**
     * Display a detailed profile view of a specific user.
     *
     * @param \App\Models\User $user The user model via route model binding.
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        // Eager load all necessary relationships to prevent N+1 query issues.
        $user->load([
            'membershipType',
            'referrer',
            'referredUsers',
            'tickets.event',
            'investments.filmProject',
            'investments.event',
            'payouts',
            'ticketRequests'
        ]);

        // Use the service class to calculate total revenue contributed.
        $revenueContributed = $this->userMetricsService->calculateRevenueContributed($user);

        return view('admin.users.show', compact('user', 'revenueContributed'));
    }
}
