<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use DOMDocument;
use DOMXPath;

class ScrapeBpomData extends Command
{
    protected $signature = 'bpom:scrape {query? : The search query for BPOM products}';
    protected $description = 'Scrape product data from BPOM website';

    protected $baseUrl = 'https://cekbpom.pom.go.id';
    protected $searchUrl = 'https://cekbpom.pom.go.id/index.php/home/produk/all';

    public function handle()
    {
        $query = $this->argument('query');

        if (!$query) {
            $query = $this->ask('Enter search query for BPOM products:');
        }

        $this->info("Searching for: {$query}");

        try {
            // Send POST request to search products
            $response = Http::asForm()->post($this->searchUrl, [
                'keyword' => $query,
                'kategori' => '',
                'submit' => 'Submit'
            ]);

            if (!$response->successful()) {
                $this->error('Failed to connect to BPOM website');
                return 1;
            }

            $dom = new DOMDocument();
            @$dom->loadHTML($response->body());
            $xpath = new DOMXPath($dom);

            // Find product links in the search results
            $productLinks = $xpath->query("//table[@class='table-bordered']//a");

            if ($productLinks->length === 0) {
                $this->warn('No products found');
                return 0;
            }

            $bar = $this->output->createProgressBar($productLinks->length);
            $bar->start();

            DB::beginTransaction();

            foreach ($productLinks as $link) {
                $productUrl = $this->baseUrl . $link->getAttribute('href');
                $this->scrapeProductPage($productUrl);
                $bar->advance();
            }

            DB::commit();
            $bar->finish();

            $this->newLine();
            $this->info('BPOM data scraping completed successfully');
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error scraping BPOM data: ' . $e->getMessage());
            return 1;
        }
    }

    protected function scrapeProductPage($url)
    {
        try {
            $response = Http::get($url);

            if (!$response->successful()) {
                return;
            }

            $dom = new DOMDocument();
            @$dom->loadHTML($response->body());
            $xpath = new DOMXPath($dom);

            // Extract product details
            // Note: Adjust XPath queries based on actual BPOM website structure
            $name = $this->extractContent($xpath, "//td[contains(text(), 'Nama Produk')]/following-sibling::td");
            $category = $this->extractContent($xpath, "//td[contains(text(), 'Kategori')]/following-sibling::td");
            $bpomCode = $this->extractContent($xpath, "//td[contains(text(), 'Nomor Registrasi')]/following-sibling::td");

            if ($name && $category) {
                DB::table('bpom_reference_data')->updateOrInsert(
                    ['bpom_code' => $bpomCode],
                    [
                        'name' => $name,
                        'category' => $category,
                        'updated_at' => now()
                    ]
                );
            }

        } catch (\Exception $e) {
            \Log::error("Error scraping product page {$url}: " . $e->getMessage());
        }
    }

    protected function extractContent(DOMXPath $xpath, string $query)
    {
        $node = $xpath->query($query)->item(0);
        return $node ? trim($node->textContent) : null;
    }
}
