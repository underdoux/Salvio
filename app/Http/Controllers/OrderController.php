<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Order::class);

        $query = $request->user()->hasRole('admin')
            ? Order::query()
            : Order::where('user_id', $request->user()->id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        if ($request->has('user') && $request->user()->can('view any orders')) {
            $query->where('user_id', $request->user);
        }

        if ($request->has('sort') && $request->has('direction')) {
            $query->orderBy($request->sort, $request->direction);
        } else {
            $query->latest();
        }

        $orders = $query->with('user')->paginate(10);

        return view('orders.index', [
            'orders' => $orders,
            'users' => $request->user()->can('view any orders') ? User::all() : null,
            'canViewAny' => $request->user()->can('view any orders'),
            'canExport' => $request->user()->can('export orders'),
        ]);
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);

        return view('orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,processing,completed,cancelled'],
        ]);

        $order->update($validated);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $order)
    {
        $this->authorize('delete', $order);

        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Order deleted successfully.');
    }

    public function export()
    {
        $this->authorize('export', Order::class);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=orders.csv',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'Order ID',
                'Customer',
                'Total',
                'Status',
                'Created At'
            ]);

            // Data
            Order::with('user')->chunk(100, function($orders) use($file) {
                foreach ($orders as $order) {
                    fputcsv($file, [
                        $order->id,
                        $order->user->name,
                        $order->total,
                        $order->status,
                        $order->created_at->format('Y-m-d H:i:s')
                    ]);
                }
            });

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
