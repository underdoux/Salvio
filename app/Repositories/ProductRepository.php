<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function all()
    {
        return Product::with('category')->orderBy('name')->get();
    }

    public function find($id)
    {
        return Product::with('category')->findOrFail($id);
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

    public function update($id, array $data)
    {
        $product = $this->find($id);
        $product->update($data);
        return $product;
    }

    public function delete($id)
    {
        $product = $this->find($id);
        return $product->delete();
    }

    public function search($query)
    {
        return Product::with('category')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('bpom_code', 'like', "%{$query}%")
            ->orWhereHas('category', function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->get();
    }

    public function getByCategory($categoryId)
    {
        return Product::with('category')
            ->where('category_id', $categoryId)
            ->orderBy('name')
            ->get();
    }

    public function getLowStock($threshold = 10)
    {
        return Product::with('category')
            ->where('stock', '<=', $threshold)
            ->where('is_by_order', false)
            ->orderBy('stock')
            ->get();
    }
}
