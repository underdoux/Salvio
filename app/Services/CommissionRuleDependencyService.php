<?php

namespace App\Services;

use App\Models\CommissionRule;
use App\Models\CommissionRuleDependency;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CommissionRuleDependencyService
{
    /**
     * Add a dependency between two commission rules.
     */
    public function addDependency(
        CommissionRule $rule,
        CommissionRule $dependsOnRule,
        string $dependencyType,
        ?string $reason = null
    ): CommissionRuleDependency {
        return CommissionRuleDependency::create([
            'commission_rule_id' => $rule->id,
            'depends_on_rule_id' => $dependsOnRule->id,
            'dependency_type' => $dependencyType,
            'reason' => $reason,
        ]);
    }

    /**
     * Check if adding a dependency would create a circular reference.
     */
    public function hasCircularDependency(CommissionRule $rule, int $dependsOnRuleId): bool
    {
        if ($rule->id === $dependsOnRuleId) {
            return true;
        }

        $visited = collect([$rule->id]);
        $toVisit = collect([$dependsOnRuleId]);

        while ($toVisit->isNotEmpty()) {
            $currentId = $toVisit->shift();

            if ($visited->contains($currentId)) {
                return true;
            }

            $visited->push($currentId);

            $dependencies = CommissionRuleDependency::where('commission_rule_id', $currentId)
                ->pluck('depends_on_rule_id');

            $toVisit = $toVisit->concat($dependencies);
        }

        return false;
    }

    /**
     * Get the dependency chain for a commission rule.
     */
    public function getDependencyChain(CommissionRule $rule): Collection
    {
        $chain = collect();
        $visited = collect();
        $this->buildDependencyChain($rule, $chain, $visited);
        return $chain;
    }

    /**
     * Build the dependency chain recursively.
     */
    protected function buildDependencyChain(
        CommissionRule $rule,
        Collection $chain,
        Collection $visited,
        int $depth = 0
    ): void {
        if ($visited->contains($rule->id)) {
            return;
        }

        $visited->push($rule->id);

        $chain->push([
            'rule' => $rule,
            'depth' => $depth,
        ]);

        $dependencies = $rule->dependencies()->with('dependsOnRule')->get();
        foreach ($dependencies as $dependency) {
            $this->buildDependencyChain($dependency->dependsOnRule, $chain, $visited, $depth + 1);
        }
    }

    /**
     * Get rules that override the given rule.
     */
    public function getOverridingRules(CommissionRule $rule): Collection
    {
        return CommissionRuleDependency::where('depends_on_rule_id', $rule->id)
            ->where('dependency_type', 'overrides')
            ->with('commissionRule')
            ->get()
            ->pluck('commissionRule');
    }

    /**
     * Get rules that are overridden by the given rule.
     */
    public function getOverriddenRules(CommissionRule $rule): Collection
    {
        return $rule->dependencies()
            ->where('dependency_type', 'overrides')
            ->with('dependsOnRule')
            ->get()
            ->pluck('dependsOnRule');
    }

    /**
     * Check if all dependencies are satisfied for a rule.
     */
    public function areDependenciesSatisfied(CommissionRule $rule): bool
    {
        $dependencies = $rule->dependencies()->with('dependsOnRule')->get();

        foreach ($dependencies as $dependency) {
            if (!$dependency->dependsOnRule->active) {
                return false;
            }

            if ($dependency->dependency_type === 'requires' && !$this->hasDateOverlap($rule, $dependency->dependsOnRule)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if two rules have overlapping effective dates.
     */
    public function hasDateOverlap(CommissionRule $ruleA, CommissionRule $ruleB): bool
    {
        $aStart = $ruleA->effective_from ?? now()->subYears(100);
        $aEnd = $ruleA->effective_until ?? now()->addYears(100);
        $bStart = $ruleB->effective_from ?? now()->subYears(100);
        $bEnd = $ruleB->effective_until ?? now()->addYears(100);

        return $aStart <= $bEnd && $bStart <= $aEnd;
    }

    /**
     * Validate and update the status of a rule based on its dependencies.
     */
    public function validateAndUpdateStatus(CommissionRule $rule): bool
    {
        $shouldBeActive = $rule->active && $this->areDependenciesSatisfied($rule);

        if ($rule->active !== $shouldBeActive) {
            $rule->update(['active' => $shouldBeActive]);
        }

        return $shouldBeActive;
    }

    /**
     * Update all rules affected by changes to the given rule.
     */
    public function updateAffectedRules(CommissionRule $rule): void
    {
        $dependentRules = CommissionRuleDependency::where('depends_on_rule_id', $rule->id)
            ->with('commissionRule')
            ->get()
            ->pluck('commissionRule');

        foreach ($dependentRules as $dependentRule) {
            $this->validateAndUpdateStatus($dependentRule);
        }
    }
}
