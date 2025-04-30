<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
        $this->middleware('auth');
        $this->middleware('role:Admin|Sales|Cashier');
    }

    public function index()
    {
        $orders = $this->orderService->getAllOrders();
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::all();
        $taxPercentage = Setting::get('tax_percentage', 10);
        $maxDiscount = Setting::get('max_discount_percentage', 20);

        return view('orders.create', compact('products', 'taxPercentage', 'maxDiscount'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validate([
                'tax' => 'required|numeric|min:0',
                'status' => 'required|string|in:' . implode(',', Order::VALID_STATUSES),
                'payment_type' => 'required|string|in:cash,installment',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.original_price' => 'required|numeric|min:0',
                'items.*.adjusted_price' => 'nullable|numeric|min:0',
                'items.*.adjustment_reason' => 'required_with:items.*.adjusted_price',
            ]);

            $order = $this->orderService->createOrder($data, $data['items']);

            DB::commit();

            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error creating order: ' . $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        $order = $this->orderService->getOrderDetails($order);
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order = $this->orderService->getOrderDetails($order);
        $products = Product::all();
        $taxPercentage = Setting::get('tax_percentage', 10);
        $maxDiscount = Setting::get('max_discount_percentage', 20);

        return view('orders.edit', compact('order', 'products', 'taxPercentage', 'maxDiscount'));
    }

    public function update(Request $request, Order $order)
    {
        try {
            DB::beginTransaction();

            $data = $request->validate([
                'status' => 'required|string|in:' . implode(',', Order::VALID_STATUSES),
                'payment_type' => 'required|string|in:cash,installment',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.original_price' => 'required|numeric|min:0',
                'items.*.adjusted_price' => 'nullable|numeric|min:0',
                'items.*.adjustment_reason' => 'required_with:items.*.adjusted_price',
            ]);

            // Update order status and payment type
            $order->update([
                'status' => $data['status'],
                'payment_type' => $data['payment_type'],
            ]);

            // Delete existing items and create new ones
            $order->items()->delete();
            foreach ($data['items'] as $item) {
                $order->items()->create($item);
            }

            // Recalculate total
            $order->updateTotal();

            DB::commit();

            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Order updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error updating order: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Order $order)
    {
        try {
            $data = $request->validate([
                'status' => 'required|string|in:' . implode(',', Order::VALID_STATUSES),
            ]);

            $this->orderService->updateOrderStatus($order, $data['status']);

            return redirect()
                ->back()
                ->with('success', 'Order status updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error updating order status: ' . $e->getMessage());
        }
    }
}
