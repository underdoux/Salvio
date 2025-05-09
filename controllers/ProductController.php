<?php
require_once __DIR__ . '/../core/Controller.php';

class ProductController extends Controller
{
    public function index()
    {
        // Placeholder for product list page
        $this->view('products/index', ['title' => 'Products']);
    }
}
?>
