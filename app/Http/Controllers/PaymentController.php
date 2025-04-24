<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
        $this->middleware('auth');
        $this->middleware('role:Admin,Cashier');
    }

    public function index($orderId)
    {
        $payments = $this->paymentService->getPaymentsByOrder($orderId);
        return view('payments.index', compact('payments', 'orderId'));
    }

    public function store(Request $request, $orderId)
    {
        $data = $request->validate([
            'payment_type' => 'required|string|in:cash,installment',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'status' => 'required|string',
        ]);

        $data['order_id'] = $orderId;

        $this->paymentService->recordPayment($data);

        return redirect()->route('payments.index', $orderId)->with('success', 'Payment recorded successfully.');
    }
}
