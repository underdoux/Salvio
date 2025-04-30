<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Http;
use DOMDocument;
use DOMXPath;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAllProducts()
    {
        return $this->productRepository->all();
    }

    public function getProductById($id)
    {
        return $this->productRepository->find($id);
    }

    public function createProduct(array $data)
    {
        if (!empty($data['bpom_code']) && empty($data['category_id'])) {
            $bpomData = $this->fetchBpomData($data['bpom_code']);
            if ($bpomData) {
                $data = array_merge($data, $bpomData);
            }
        }

        $product = $this->productRepository->create($data);

        if (empty($data['category_id'])) {
            $product->assignCategoryFromBpom();
        }

        return $product;
    }

    public function updateProduct($id, array $data)
    {
        if (!empty($data['bpom_code']) && empty($data['category_id'])) {
            $bpomData = $this->fetchBpomData($data['bpom_code']);
            if ($bpomData) {
                $data = array_merge($data, $bpomData);
            }
        }

        return $this->productRepository->update($id, $data);
    }

    public function deleteProduct($id)
    {
        return $this->productRepository->delete($id);
    }

    public function searchProducts($query)
    {
        return $this->productRepository->search($query);
    }

    protected function fetchBpomData($bpomCode)
    {
        try {
            $response = Http::get('https://cekbpom.pom.go.id/index.php/home/produk/'. $bpomCode);

            if ($response->successful()) {
                $dom = new DOMDocument();
                @$dom->loadHTML($response->body());

                $xpath = new DOMXPath($dom);

                // Extract product details from the BPOM website
                // Note: You'll need to adjust these XPath queries based on the actual structure of the BPOM website
                $name = $xpath->query("//td[contains(text(), 'Nama Produk')]/following-sibling::td")->item(0)?->textContent;
                $category = $xpath->query("//td[contains(text(), 'Kategori')]/following-sibling::td")->item(0)?->textContent;

                if ($name && $category) {
                    return [
                        'name' => trim($name),
                        'category' => trim($category)
                    ];
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error fetching BPOM data: ' . $e->getMessage());
        }

        return null;
    }
}
