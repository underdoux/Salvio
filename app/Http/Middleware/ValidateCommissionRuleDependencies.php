<?php

namespace App\Http\Middleware;

use App\Models\CommissionRule;
use App\Services\CommissionRuleDependencyService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateCommissionRuleDependencies
{
    protected CommissionRuleDependencyService $dependencyService;

    public function __construct(CommissionRuleDependencyService $dependencyService)
    {
        $this->dependencyService = $dependencyService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var CommissionRule|null */
        $commissionRule = $request->route('commissionRule');

        if (!$commissionRule instanceof CommissionRule) {
            return $next($request);
        }

        // If trying to activate a rule
        if ($request->has('active') && filter_var($request->get('active'), FILTER_VALIDATE_BOOLEAN)) {
            // Check if all dependencies are satisfied
            if (!$this->dependencyService->areDependenciesSatisfied($commissionRule)) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'dependencies' => 'Cannot activate rule: One or more required dependencies are not satisfied.',
                    ]);
            }
        }

        // If updating rule conditions or dates
        if ($request->has('conditions') || $request->has('effective_from') || $request->has('effective_until')) {
            // Get all dependent rules
            $dependentRules = $commissionRule->dependents;

            foreach ($dependentRules as $dependent) {
                $rule = $dependent->commissionRule;

                // If the dependent rule is active and would be invalidated by this change
                if ($rule->active && !$this->dependencyService->areDependenciesSatisfied($rule)) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'dependencies' => 'Cannot update rule: This would invalidate active dependent rules.',
                        ]);
                }
            }
        }

        // If deactivating a rule
        if ($request->has('active') && !filter_var($request->get('active'), FILTER_VALIDATE_BOOLEAN)) {
            // Get all dependent rules that require this rule
            $dependentRules = $commissionRule->dependents()
                ->where('dependency_type', 'requires')
                ->with('commissionRule')
                ->get();

            // Check if any dependent rules are active
            $activeDependent = $dependentRules->first(function ($dependent) {
                return $dependent->commissionRule->active;
            });

            if ($activeDependent) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'dependencies' => 'Cannot deactivate rule: Active rules depend on this rule.',
                    ]);
            }
        }

        return $next($request);
    }
}
