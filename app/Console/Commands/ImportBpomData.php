<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use League\Csv\Exception;

class ImportBpomData extends Command
{
    protected $signature = 'import:bpom {file}';
    protected $description = 'Import BPOM medicine data from CSV file';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        try {
            $csv = Reader::createFromPath($filePath, 'r');
            $csv->setHeaderOffset(0);
            $records = $csv->getRecords(['name', 'category']);

            DB::beginTransaction();

            foreach ($records as $record) {
                DB::table('bpom_reference_data')->updateOrInsert(
                    ['name' => $record['name']],
                    ['category' => $record['category']]
                );
            }

            DB::commit();

            $this->info('BPOM data imported successfully.');
            return 0;
        } catch (Exception $e) {
            DB::rollBack();
            $this->error('Error importing BPOM data: ' . $e->getMessage());
            return 1;
        }
    }
}
