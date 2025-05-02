<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionRuleDependency extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'commission_rule_id',
        'depends_on_rule_id',
        'dependency_type',
        'reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'dependency_type' => 'string',
    ];

    /**
     * Get the commission rule that owns this dependency.
     */
    public function commissionRule(): BelongsTo
    {
        return $this->belongsTo(CommissionRule::class);
    }

    /**
     * Get the commission rule that this rule depends on.
     */
    public function dependsOnRule(): BelongsTo
    {
        return $this->belongsTo(CommissionRule::class, 'depends_on_rule_id');
    }

    /**
     * Check if this dependency is valid.
     */
    public function isValid(): bool
    {
        $dependsOnRule = $this->dependsOnRule;

        // Check if the dependent rule is active and within its effective dates
        if (!$dependsOnRule->isActive()) {
            return false;
        }

        // For 'requires' dependencies, both rules must be active
        if ($this->dependency_type === 'requires' && !$this->commissionRule->isActive()) {
            return false;
        }

        // For 'conflicts' dependencies, check if there's an actual conflict
        if ($this->dependency_type === 'conflicts') {
            return !$this->hasConflict();
        }

        // For 'overrides' dependencies, check if the rules have overlapping conditions
        if ($this->dependency_type === 'overrides') {
            return $this->hasOverlappingConditions();
        }

        return true;
    }

    /**
     * Check if there's a conflict between the rules.
     */
    private function hasConflict(): bool
    {
        $ruleA = $this->commissionRule;
        $ruleB = $this->dependsOnRule;

        // Check for date overlap
        if ($this->hasDateOverlap($ruleA, $ruleB)) {
            return true;
        }

        // Check for condition overlap
        if ($this->hasConditionOverlap($ruleA, $ruleB)) {
            return true;
        }

        return false;
    }

    /**
     * Check if two rules have overlapping effective dates.
     */
    private function hasDateOverlap(CommissionRule $ruleA, CommissionRule $ruleB): bool
    {
        if (!$ruleA->effective_from && !$ruleA->effective_until &&
            !$ruleB->effective_from && !$ruleB->effective_until) {
            return true;
        }

        $aStart = $ruleA->effective_from ?? now()->subYears(100);
        $aEnd = $ruleA->effective_until ?? now()->addYears(100);
        $bStart = $ruleB->effective_from ?? now()->subYears(100);
        $bEnd = $ruleB->effective_until ?? now()->addYears(100);

        return $aStart <= $bEnd && $bStart <= $aEnd;
    }

    /**
     * Check if two rules have overlapping conditions.
     */
    private function hasConditionOverlap(CommissionRule $ruleA, CommissionRule $ruleB): bool
    {
        $conditionsA = $ruleA->conditions ?? [];
        $conditionsB = $ruleB->conditions ?? [];

        // Check product categories overlap
        if (isset($conditionsA['product_categories']) && isset($conditionsB['product_categories'])) {
            $categoriesA = (array) $conditionsA['product_categories'];
            $categoriesB = (array) $conditionsB['product_categories'];
            if (array_intersect($categoriesA, $categoriesB)) {
                return true;
            }
        }

        // Check order value ranges overlap
        if ($this->hasRangeOverlap(
            $conditionsA['min_order_value'] ?? null,
            $conditionsA['max_order_value'] ?? null,
            $conditionsB['min_order_value'] ?? null,
            $conditionsB['max_order_value'] ?? null
        )) {
            return true;
        }

        // Check quantity ranges overlap
        if ($this->hasRangeOverlap(
            $conditionsA['minimum_quantity'] ?? null,
            $conditionsA['maximum_quantity'] ?? null,
            $conditionsB['minimum_quantity'] ?? null,
            $conditionsB['maximum_quantity'] ?? null
        )) {
            return true;
        }

        return false;
    }

    /**
     * Check if two ranges overlap.
     */
    private function hasRangeOverlap($minA, $maxA, $minB, $maxB): bool
    {
        if ($minA === null && $maxA === null && $minB === null && $maxB === null) {
            return true;
        }

        $minA = $minA ?? PHP_FLOAT_MIN;
        $maxA = $maxA ?? PHP_FLOAT_MAX;
        $minB = $minB ?? PHP_FLOAT_MIN;
        $maxB = $maxB ?? PHP_FLOAT_MAX;

        return $minA <= $maxB && $minB <= $maxA;
    }

    /**
     * Check if the rules have overlapping conditions (for override validation).
     */
    private function hasOverlappingConditions(): bool
    {
        return $this->hasConditionOverlap($this->commissionRule, $this->dependsOnRule);
    }
}
