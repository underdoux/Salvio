<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapitalInvestor extends Model
{
    protected $fillable = [
        'name',
        'ownership_percentage',
    ];
}
