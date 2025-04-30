<?php

namespace App\Http\Controllers;

use App\Models\CommissionRule;
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class CommissionController extends Controller
{
    protected $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
        $this->middleware('auth');

        // Only admin can configure commission rules
        $this->middleware('role:Admin')->only([
            'updateRules',
            'deleteRule',
            'createRule'
        ]);
    }

    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $requestedUserId = $request->input('user_id', $currentUser->id);

        // Sales can only view their own commissions
        if (!$currentUser->hasRole('Admin') && $requestedUserId != $currentUser->id) {
            abort(403, 'Unauthorized access - You can only view your own commissions');
        }

        $userId = $requestedUserId;
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $commissions = $this->commissionService->getCommissionsByUser(
            $userId,
            $startDate,
            $endDate
        );

        return view('commissions.index', compact('commissions'));
    }

    public function summary(Request $request)
    {
        $currentUser = Auth::user();
        $requestedUserId = $request->input('user_id', $currentUser->id);

        // Sales can only view their own summary
        if (!$currentUser->hasRole('Admin') && $requestedUserId != $currentUser->id) {
            abort(403, 'Unauthorized access - You can only view your own commission summary');
        }

        $userId = $requestedUserId;
        $period = $request->get('period', 'month');

        $summary = $this->commissionService->getCommissionSummary($userId, $period);

        return view('commissions.summary', compact('summary'));
    }

    public function rules()
    {
        $this->authorize('configure commissions');

        $rules = CommissionRule::with(['reference'])->get();
        return view('commissions.rules', compact('rules'));
    }

    public function createRule(Request $request)
    {
        $this->authorize('configure commissions');

        $validated = $request->validate([
            'type' => 'required|in:' . implode(',', [
                CommissionRule::TYPE_GLOBAL,
                CommissionRule::TYPE_CATEGORY,
                CommissionRule::TYPE_PRODUCT
            ]),
            'reference_id' => 'required_unless:type,global|nullable|integer',
            'rate' => 'required|numeric|min:0|max:100',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gt:min_amount',
        ]);

        $this->commissionService->updateCommissionRules([$validated]);

        return redirect()
            ->route('commissions.rules')
            ->with('success', 'Commission rule created successfully');
    }

    public function updateRules(Request $request)
    {
        $this->authorize('configure commissions');

        $validated = $request->validate([
            'rules' => 'required|array',
            'rules.*.type' => 'required|in:' . implode(',', [
                CommissionRule::TYPE_GLOBAL,
                CommissionRule::TYPE_CATEGORY,
                CommissionRule::TYPE_PRODUCT
            ]),
            'rules.*.reference_id' => 'required_unless:rules.*.type,global|nullable|integer',
            'rules.*.rate' => 'required|numeric|min:0|max:100',
            'rules.*.min_amount' => 'nullable|numeric|min:0',
            'rules.*.max_amount' => 'nullable|numeric|min:0|gt:rules.*.min_amount',
        ]);

        $this->commissionService->updateCommissionRules($validated['rules']);

        return redirect()
            ->route('commissions.rules')
            ->with('success', 'Commission rules updated successfully');
    }

    public function deleteRule(CommissionRule $rule)
    {
        $this->authorize('configure commissions');

        $rule->delete();

        return redirect()
            ->route('commissions.rules')
            ->with('success', 'Commission rule deleted successfully');
    }

    public function export(Request $request)
    {
        $currentUser = Auth::user();

        // Only admin can export all commissions
        if (!$currentUser->hasRole('Admin')) {
            abort(403, 'Unauthorized access - Only administrators can export commission data');
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $userId = $request->input('user_id');

        $commissions = $this->commissionService->getCommissionsByUser(
            $userId,
            $startDate,
            $endDate
        );

        // Generate CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=commissions.csv',
        ];

        $callback = function() use ($commissions) {
            $file = fopen('php://output', 'w');

            // Add headers
            fputcsv($file, [
                'Date',
                'Order ID',
                'Product',
                'Category',
                'Original Price',
                'Commission Amount',
                'Status'
            ]);

            // Add data rows
            foreach ($commissions as $commission) {
                fputcsv($file, [
                    $commission->created_at->format('Y-m-d'),
                    $commission->orderItem->order_id,
                    $commission->orderItem->product->name,
                    $commission->orderItem->product->category->name,
                    $commission->orderItem->original_price,
                    $commission->amount,
                    $commission->status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
