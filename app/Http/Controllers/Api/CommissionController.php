<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CommissionController extends Controller
{
    protected $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
        $this->middleware('auth:sanctum');
        $this->middleware('role:Admin')->only(['approve', 'reject', 'markAsPaid']);
    }

    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $requestedUserId = $request->input('user_id', $user->id);

        // Validate user can view these commissions
        if (!$user->hasRole('Admin') && $requestedUserId !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized access - You can only view your own commissions'
            ], 403);
        }

        $userId = $requestedUserId;
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        $status = $request->get('status');
        $period = $request->get('period', 'month');

        $query = Commission::with(['orderItem.product', 'orderItem.order'])
            ->where('user_id', $userId);

        // Apply date filters
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            switch ($period) {
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                    break;
                case 'year':
                    $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]);
                    break;
            }
        }

        // Apply status filter
        if ($status) {
            $query->where('status', $status);
        }

        // Get paginated results
        $commissions = $query->latest()->paginate(10);

        // Calculate summary
        $summary = [
            'total' => $query->sum('amount'),
            'pending' => $query->where('status', Commission::STATUS_PENDING)->sum('amount'),
            'paid' => $query->where('status', Commission::STATUS_PAID)->sum('amount')
        ];

        return response()->json([
            'data' => $commissions->items(),
            'meta' => [
                'current_page' => $commissions->currentPage(),
                'from' => $commissions->firstItem(),
                'to' => $commissions->lastItem(),
                'total' => $commissions->total(),
                'links' => $commissions->linkCollection()->toArray()
            ],
            'summary' => $summary
        ]);
    }

    public function approve(Commission $commission): JsonResponse
    {
        if ($commission->status !== Commission::STATUS_PENDING) {
            return response()->json([
                'message' => 'Commission cannot be approved - invalid status'
            ], 422);
        }

        $commission->approve();

        return response()->json([
            'message' => 'Commission approved successfully',
            'commission' => $commission->fresh()
        ]);
    }

    public function reject(Request $request, Commission $commission): JsonResponse
    {
        if ($commission->status !== Commission::STATUS_PENDING) {
            return response()->json([
                'message' => 'Commission cannot be rejected - invalid status'
            ], 422);
        }

        $validated = $request->validate([
            'notes' => 'required|string|max:255'
        ]);

        $commission->reject($validated['notes']);

        return response()->json([
            'message' => 'Commission rejected successfully',
            'commission' => $commission->fresh()
        ]);
    }

    public function markAsPaid(Commission $commission): JsonResponse
    {
        if ($commission->status !== Commission::STATUS_APPROVED) {
            return response()->json([
                'message' => 'Commission cannot be marked as paid - invalid status'
            ], 422);
        }

        $commission->markAsPaid();

        return response()->json([
            'message' => 'Commission marked as paid successfully',
            'commission' => $commission->fresh()
        ]);
    }
}
