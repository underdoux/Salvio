<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        parent::__construct(); // Initialize parent controller
        $this->orderService = $orderService;
        $this->middleware('auth');
        $this->middleware('role:Admin,Sales,Cashier');
    }

    public function index()
    {
        $orders = $this->orderService->getAllOrders();
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        return view('orders.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tax' => 'required|numeric|min:0',
            'status' => 'required|string',
            'total' => 'required|numeric|min:0',
            'payment_type' => 'required|string|in:cash,installment',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.original_price' => 'required|numeric|min:0',
            'items.*.adjusted_price' => 'nullable|numeric|min:0',
            'items.*.adjustment_reason' => 'nullable|string',
        ]);

        $orderData = [
            'tax' => $data['tax'],
            'status' => $data['status'],
            'total' => $data['total'],
            'payment_type' => $data['payment_type'],
        ];

        $itemsData = $data['items'];

        $order = $this->orderService->createOrder($orderData, $itemsData);

        return redirect()->route('orders.index')->with('success', 'Order created successfully.');
    }
}
