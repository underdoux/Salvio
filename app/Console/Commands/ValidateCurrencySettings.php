<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;

class ValidateCurrencySettings extends Command
{
    protected $signature = 'settings:validate-currency {--fix : Fix any invalid settings}';
    protected $description = 'Validate currency settings and optionally fix them';

    public function handle()
    {
        $issues = [];
        $settings = Setting::whereIn('key', [
            'currency_code',
            'currency_symbol',
            'currency_position',
            'currency_symbols'
        ])->get();

        // Check currency position
        $position = $settings->firstWhere('key', 'currency_position')?->value;
        if (!in_array($position, ['before', 'after'])) {
            $issues[] = "Invalid currency position: {$position}";
            if ($this->option('fix')) {
                Setting::updateOrCreate(
                    ['key' => 'currency_position'],
                    ['value' => 'before']
                );
            }
        }

        // Check currency code
        $code = $settings->firstWhere('key', 'currency_code')?->value;
        $symbols = json_decode($settings->firstWhere('key', 'currency_symbols')?->value ?? '{}', true);
        if (!array_key_exists($code, $symbols)) {
            $issues[] = "Invalid currency code: {$code}";
            if ($this->option('fix')) {
                Setting::updateOrCreate(
                    ['key' => 'currency_code'],
                    ['value' => 'IDR']
                );
                Setting::updateOrCreate(
                    ['key' => 'currency_symbol'],
                    ['value' => 'Rp']
                );
            }
        }

        if (empty($issues)) {
            $this->info('✓ All currency settings are valid.');
            return 0;
        }

        if ($this->option('fix')) {
            $this->info('Settings have been fixed successfully.');
            return 0;
        }

        $this->error('Found currency setting issues:');
        foreach ($issues as $issue) {
            $this->line("- {$issue}");
        }
        return 1;
    }
}
