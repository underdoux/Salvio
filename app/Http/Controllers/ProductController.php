<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;
use App\Models\Category;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
        $this->middleware('auth');
        $this->middleware('role:Admin|Sales')->only(['create', 'store']);
        $this->middleware('role:Admin')->only(['edit', 'update', 'destroy']);
    }

    public function index()
    {
        $products = $this->productService->getAllProducts();
        $categories = Category::all();
        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'bpom_code' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_by_order' => 'boolean'
        ]);

        $validated['is_by_order'] = $request->has('is_by_order');

        $product = $this->productService->createProduct($validated);

        if (!$validated['category_id']) {
            $product->assignCategoryFromBpom();
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully');
    }

    public function edit($id)
    {
        $product = $this->productService->getProductById($id);
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'bpom_code' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_by_order' => 'boolean'
        ]);

        $validated['is_by_order'] = $request->has('is_by_order');

        $product = $this->productService->updateProduct($id, $validated);

        if (!$validated['category_id']) {
            $product->assignCategoryFromBpom();
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    public function destroy($id)
    {
        $this->productService->deleteProduct($id);
        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        $products = $this->productService->searchProducts($query);
        return response()->json($products);
    }
}
