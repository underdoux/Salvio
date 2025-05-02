<?php

namespace App\Services;

use App\Models\CommissionRule;
use App\Models\CommissionRuleConflict;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommissionRuleConflictService
{
    /**
     * Detect conflicts between all commission rules.
     */
    public function detectAllConflicts(): Collection
    {
        $rules = CommissionRule::active()->get();
        $conflicts = collect();

        foreach ($rules as $ruleA) {
            foreach ($rules as $ruleB) {
                if ($ruleA->id >= $ruleB->id) {
                    continue;
                }

                $conflict = $this->detectConflict($ruleA, $ruleB);
                if ($conflict) {
                    $conflicts->push($conflict);
                }
            }
        }

        return $conflicts;
    }

    /**
     * Detect conflicts for a specific commission rule.
     */
    public function detectRuleConflicts(CommissionRule $rule): Collection
    {
        $otherRules = CommissionRule::active()
            ->where('id', '!=', $rule->id)
            ->get();

        $conflicts = collect();

        foreach ($otherRules as $otherRule) {
            $conflict = $this->detectConflict($rule, $otherRule);
            if ($conflict) {
                $conflicts->push($conflict);
            }
        }

        return $conflicts;
    }

    /**
     * Detect conflict between two commission rules.
     */
    protected function detectConflict(CommissionRule $ruleA, CommissionRule $ruleB): ?CommissionRuleConflict
    {
        $conflicts = [];

        // Check for type conflicts
        if ($ruleA->type !== $ruleB->type) {
            $conflicts['type_mismatch'] = [
                'rule_a_type' => $ruleA->type,
                'rule_b_type' => $ruleB->type,
            ];
        }

        // Check for value conflicts
        if ($ruleA->type === $ruleB->type && $ruleA->value !== $ruleB->value) {
            $conflicts['value_conflict'] = [
                'value_difference' => abs($ruleA->value - $ruleB->value),
            ];
        }

        // Check for date overlaps
        if ($this->hasDateOverlap($ruleA, $ruleB)) {
            $conflicts['date_overlap'] = [
                'start' => max(
                    $ruleA->effective_from ?? now()->subYears(100),
                    $ruleB->effective_from ?? now()->subYears(100)
                )->toDateTimeString(),
                'end' => min(
                    $ruleA->effective_until ?? now()->addYears(100),
                    $ruleB->effective_until ?? now()->addYears(100)
                )->toDateTimeString(),
            ];
        }

        // Check for condition overlaps
        $conditionOverlap = $this->findConditionOverlap($ruleA->conditions, $ruleB->conditions);
        if (!empty($conditionOverlap)) {
            $conflicts['condition_overlap'] = $conditionOverlap;
        }

        if (empty($conflicts)) {
            return null;
        }

        return CommissionRuleConflict::create([
            'rule_a_id' => $ruleA->id,
            'rule_b_id' => $ruleB->id,
            'conflict_type' => array_key_first($conflicts),
            'details' => $conflicts,
        ]);
    }

    /**
     * Check if two rules have overlapping effective dates.
     */
    protected function hasDateOverlap(CommissionRule $ruleA, CommissionRule $ruleB): bool
    {
        $aStart = $ruleA->effective_from ?? now()->subYears(100);
        $aEnd = $ruleA->effective_until ?? now()->addYears(100);
        $bStart = $ruleB->effective_from ?? now()->subYears(100);
        $bEnd = $ruleB->effective_until ?? now()->addYears(100);

        return $aStart <= $bEnd && $bStart <= $aEnd;
    }

    /**
     * Find overlapping conditions between two rules.
     */
    protected function findConditionOverlap(?array $conditionsA, ?array $conditionsB): array
    {
        if (!$conditionsA || !$conditionsB) {
            return [];
        }

        $overlap = [];

        // Check numeric ranges
        foreach (['minimum_order_amount', 'maximum_order_amount'] as $field) {
            if (isset($conditionsA[$field], $conditionsB[$field])) {
                $minA = $conditionsA[$field];
                $minB = $conditionsB[$field];
                if ($minA === $minB) {
                    $overlap[$field] = $minA;
                }
            }
        }

        // Check arrays
        foreach (['product_categories', 'customer_groups', 'payment_methods'] as $field) {
            if (isset($conditionsA[$field], $conditionsB[$field])) {
                $intersect = array_intersect($conditionsA[$field], $conditionsB[$field]);
                if (!empty($intersect)) {
                    $overlap[$field] = $intersect;
                }
            }
        }

        return $overlap;
    }

    /**
     * Resolve a conflict with the given resolution data.
     */
    public function resolveConflict(CommissionRuleConflict $conflict, string $resolutionType, array $resolutionData, string $notes): bool
    {
        DB::beginTransaction();

        try {
            switch ($resolutionType) {
                case 'adjust_conditions':
                    $this->resolveWithConditions($conflict, $resolutionData);
                    break;
                case 'adjust_values':
                    $this->resolveWithValues($conflict, $resolutionData);
                    break;
                case 'adjust_dates':
                    $this->resolveWithDates($conflict, $resolutionData);
                    break;
                default:
                    throw new \InvalidArgumentException("Invalid resolution type: {$resolutionType}");
            }

            $conflict->update([
                'resolved' => true,
                'resolved_at' => now(),
                'resolved_by' => Auth::id(),
                'resolution_type' => $resolutionType,
                'resolution_data' => $resolutionData,
                'resolution_notes' => $notes,
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Resolve conflict by adjusting conditions.
     */
    protected function resolveWithConditions(CommissionRuleConflict $conflict, array $data): void
    {
        if (isset($data['rule_a_conditions'])) {
            $conflict->ruleA->update(['conditions' => $data['rule_a_conditions']]);
        }

        if (isset($data['rule_b_conditions'])) {
            $conflict->ruleB->update(['conditions' => $data['rule_b_conditions']]);
        }
    }

    /**
     * Resolve conflict by adjusting values.
     */
    protected function resolveWithValues(CommissionRuleConflict $conflict, array $data): void
    {
        if (isset($data['rule_a_value'])) {
            $conflict->ruleA->update(['value' => $data['rule_a_value']]);
        }

        if (isset($data['rule_b_value'])) {
            $conflict->ruleB->update(['value' => $data['rule_b_value']]);
        }
    }

    /**
     * Resolve conflict by adjusting dates.
     */
    protected function resolveWithDates(CommissionRuleConflict $conflict, array $data): void
    {
        if (isset($data['rule_a_effective_from'], $data['rule_a_effective_until'])) {
            $conflict->ruleA->update([
                'effective_from' => $data['rule_a_effective_from'],
                'effective_until' => $data['rule_a_effective_until'],
            ]);
        }

        if (isset($data['rule_b_effective_from'], $data['rule_b_effective_until'])) {
            $conflict->ruleB->update([
                'effective_from' => $data['rule_b_effective_from'],
                'effective_until' => $data['rule_b_effective_until'],
            ]);
        }
    }
}
