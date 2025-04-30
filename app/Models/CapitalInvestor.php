<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CapitalInvestor extends Model
{
    protected $fillable = [
        'name',
        'email',
        'investment_amount',
        'investment_date',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'investment_amount' => 'decimal:2',
        'investment_date' => 'date',
        'is_active' => 'boolean'
    ];

    public function profitDistributions(): HasMany
    {
        return $this->hasMany(ProfitDistribution::class);
    }

    public function getTotalInvestedAttribute(): float
    {
        return static::where('is_active', true)->sum('investment_amount');
    }

    public function getSharePercentageAttribute(): float
    {
        $totalInvested = $this->getTotalInvestedAttribute();
        if ($totalInvested <= 0) {
            return 0;
        }
        return ($this->investment_amount / $totalInvested) * 100;
    }

    public function getTotalDistributedProfitAttribute(): float
    {
        return $this->profitDistributions()
            ->where('status', 'approved')
            ->sum('profit_share');
    }

    public function getPendingDistributionsAttribute(): float
    {
        return $this->profitDistributions()
            ->where('status', 'pending')
            ->sum('profit_share');
    }

    public function getDistributionSummaryByPeriod($startDate = null, $endDate = null)
    {
        $query = $this->profitDistributions();

        if ($startDate) {
            $query->where('period_start', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('period_end', '<=', $endDate);
        }

        $distributions = $query->get();

        return [
            'total_revenue' => $distributions->sum('total_revenue'),
            'total_cost' => $distributions->sum('total_cost'),
            'net_profit' => $distributions->sum('net_profit'),
            'profit_share' => $distributions->sum('profit_share'),
            'approved_amount' => $distributions->where('status', 'approved')->sum('profit_share'),
            'pending_amount' => $distributions->where('status', 'pending')->sum('profit_share'),
            'distribution_count' => $distributions->count(),
            'approved_count' => $distributions->where('status', 'approved')->count(),
            'pending_count' => $distributions->where('status', 'pending')->count(),
            'average_share_percentage' => $distributions->avg('share_percentage')
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    public function activate()
    {
        $this->update(['is_active' => true]);
    }
}
