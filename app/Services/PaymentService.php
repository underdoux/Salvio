<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function recordPayment(array $data)
    {
        return DB::transaction(function () use ($data) {
            $payment = Payment::create($data);
            // Additional logic to update order or supplier/customer status
            return $payment;
        });
    }

    public function getPaymentsByOrder($orderId)
    {
        return Payment::where('order_id', $orderId)->get();
    }
}
