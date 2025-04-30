<?php

namespace Database\Seeders;

use App\Models\CapitalInvestor;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CapitalInvestorSeeder extends Seeder
{
    public function run()
    {
        $investors = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'investment_amount' => 500000000, // 500M
                'investment_date' => Carbon::now()->subYears(2),
                'notes' => 'Initial founding investor',
                'is_active' => true,
                'ownership_percentage' => 50.00 // 50%
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@example.com',
                'investment_amount' => 300000000, // 300M
                'investment_date' => Carbon::now()->subYears(1),
                'notes' => 'Series A investor',
                'is_active' => true,
                'ownership_percentage' => 30.00 // 30%
            ],
            [
                'name' => 'Michael Chen',
                'email' => 'michael.chen@example.com',
                'investment_amount' => 200000000, // 200M
                'investment_date' => Carbon::now()->subMonths(6),
                'notes' => 'Strategic partner',
                'is_active' => true,
                'ownership_percentage' => 20.00 // 20%
            ]
        ];

        foreach ($investors as $investor) {
            CapitalInvestor::create($investor);
        }
    }
}
