<?php

namespace Database\Seeders;

use App\Models\CommissionRule;
use Illuminate\Database\Seeder;

class CommissionRuleSeeder extends Seeder
{
    public function run()
    {
        // Create default global commission rule
        CommissionRule::create([
            'type' => CommissionRule::TYPE_GLOBAL,
            'reference_id' => null,
            'rate' => 5.00, // 5% default commission
            'min_amount' => 1.00,
            'max_amount' => 1000.00,
            'is_active' => true
        ]);

        // Create some category-specific rules
        CommissionRule::create([
            'type' => CommissionRule::TYPE_CATEGORY,
            'reference_id' => 1, // Assuming category ID 1 exists
            'rate' => 7.50, // 7.5% commission for this category
            'min_amount' => 2.00,
            'max_amount' => 1500.00,
            'is_active' => true
        ]);

        // Create some product-specific rules
        CommissionRule::create([
            'type' => CommissionRule::TYPE_PRODUCT,
            'reference_id' => 1, // Assuming product ID 1 exists
            'rate' => 10.00, // 10% commission for this product
            'min_amount' => 5.00,
            'max_amount' => 2000.00,
            'is_active' => true
        ]);
    }
}
?>
