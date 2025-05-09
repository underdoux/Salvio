<?php
require_once __DIR__ . '/../core/Controller.php';

class OrderController extends Controller
{
    public function index()
    {
        // Placeholder for orders page
        $this->view('orders/index', ['title' => 'Orders']);
    }
}
?>
