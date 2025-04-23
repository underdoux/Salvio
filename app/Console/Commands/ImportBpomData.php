<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class ImportBpomData extends Command
{
    protected $signature = 'bpom:import {file}';

    protected $description = 'Import BPOM medicine data from CSV file';

    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return 1;
        }

        $csv = Reader::createFromPath($file, 'r');
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();

        DB::table('bpom_reference_data')->truncate();

        $count = 0;
        foreach ($records as $record) {
            DB::table('bpom_reference_data')->insert([
                'name' => $record['name'],
                'category' => $record['category'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $count++;
        }

        $this->info("Imported $count BPOM records.");

        return 0;
    }
}
