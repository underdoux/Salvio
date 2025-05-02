<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\CommissionRule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Commission::with('user');

        if (!$user->can('view any commissions')) {
            $query->where('user_id', $user->id);
        }

        if ($request->has('user') && $user->can('view any commissions')) {
            $query->where('user_id', $request->user);
        }

        if ($request->has('period')) {
            $query->whereBetween('created_at', [
                $request->period . '-01',
                $request->period . '-31'
            ]);
        }

        $commissions = $query->paginate(10);
        $users = $user->can('view any commissions') ? User::all() : null;

        return view('commissions.index', [
            'commissions' => $commissions,
            'users' => $users,
            'canViewAny' => $user->can('view any commissions'),
            'canExport' => $user->can('export commissions'),
            'canManageRules' => $user->can('manage commission rules')
        ]);
    }

    public function reports()
    {
        $this->authorize('view any commissions');

        $data = [
            'total_commissions' => Commission::sum('amount'),
            'commission_by_role' => $this->getCommissionsByRole(),
            'top_earners' => $this->getTopEarners(),
            'monthly_trends' => $this->getMonthlyTrends()
        ];

        return view('commissions.reports', compact('data'));
    }

    public function export()
    {
        $this->authorize('export commissions');

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=commissions.csv',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'ID',
                'User',
                'Amount',
                'Type',
                'Order ID',
                'Created At'
            ]);

            // Data
            Commission::with('user')->chunk(100, function($commissions) use($file) {
                foreach ($commissions as $commission) {
                    fputcsv($file, [
                        $commission->id,
                        $commission->user->name,
                        $commission->amount,
                        $commission->type,
                        $commission->order_id,
                        $commission->created_at->format('Y-m-d H:i:s')
                    ]);
                }
            });

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    private function getCommissionsByRole()
    {
        return Commission::join('users', 'commissions.user_id', '=', 'users.id')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->selectRaw('roles.name as role, SUM(commissions.amount) as total')
            ->groupBy('roles.name')
            ->get();
    }

    private function getTopEarners()
    {
        return Commission::with('user')
            ->select('user_id')
            ->selectRaw('SUM(amount) as total')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
    }

    private function getMonthlyTrends()
    {
        return Commission::selectRaw('strftime("%Y-%m", created_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get();
    }
}
