<?php
require_once __DIR__ . '/../core/Controller.php';

class SalesController extends Controller
{
    public function index()
    {
        // Placeholder for sales page
        $this->view('sales/index', ['title' => 'Sales']);
    }
}
?>
