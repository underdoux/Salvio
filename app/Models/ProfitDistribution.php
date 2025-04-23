<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfitDistribution extends Model
{
    protected $fillable = [
        'capital_investor_id',
        'amount',
        'distribution_date',
    ];
}
