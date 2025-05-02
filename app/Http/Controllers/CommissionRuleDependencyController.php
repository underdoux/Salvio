<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommissionRuleDependencyRequest;
use App\Models\CommissionRule;
use App\Models\CommissionRuleDependency;
use App\Services\CommissionRuleDependencyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CommissionRuleDependencyController extends Controller
{
    protected CommissionRuleDependencyService $dependencyService;

    public function __construct(CommissionRuleDependencyService $dependencyService)
    {
        $this->dependencyService = $dependencyService;
    }

    /**
     * Display a listing of dependencies for a commission rule.
     */
    public function index(CommissionRule $commissionRule): View
    {
        $dependencies = $commissionRule->dependencies()->with('dependsOnRule')->get();
        $dependents = $commissionRule->dependents()->with('commissionRule')->get();

        return view('commission-rules.dependencies.index', [
            'rule' => $commissionRule,
            'dependencies' => $dependencies,
            'dependents' => $dependents,
        ]);
    }

    /**
     * Show the dependency graph for a commission rule.
     */
    public function graph(CommissionRule $commissionRule): View
    {
        $chain = $this->dependencyService->getDependencyChain($commissionRule);

        $nodes = $chain->map(function ($item) {
            return [
                'id' => $item['rule']->id,
                'label' => $item['rule']->name,
                'level' => $item['depth'],
            ];
        });

        $edges = collect();
        $chain->each(function ($item) use ($edges) {
            $rule = $item['rule'];
            $rule->dependencies->each(function ($dependency) use ($edges, $rule) {
                $edges->push([
                    'from' => $rule->id,
                    'to' => $dependency->depends_on_rule_id,
                    'type' => $dependency->dependency_type,
                ]);
            });
        });

        return view('commission-rules.dependencies.graph', [
            'rule' => $commissionRule,
            'nodes' => $nodes,
            'edges' => $edges,
        ]);
    }

    /**
     * Show the dependency analysis for a commission rule.
     */
    public function analyze(CommissionRule $commissionRule): View
    {
        $analysis = [
            'circular_dependencies' => [],
            'missing_dependencies' => [],
            'date_conflicts' => [],
            'value_conflicts' => [],
        ];

        // Check for circular dependencies
        $chain = $this->dependencyService->getDependencyChain($commissionRule);
        $visited = collect();
        $chain->each(function ($item) use (&$analysis, $visited) {
            if ($visited->contains($item['rule']->id)) {
                $analysis['circular_dependencies'][] = $item['rule'];
            }
            $visited->push($item['rule']->id);
        });

        // Check for missing dependencies
        $commissionRule->dependencies->each(function ($dependency) use (&$analysis) {
            if (!$dependency->dependsOnRule->active) {
                $analysis['missing_dependencies'][] = $dependency;
            }
        });

        // Check for date conflicts
        $commissionRule->dependencies->each(function ($dependency) use (&$analysis) {
            if ($dependency->dependency_type === 'requires' &&
                !$this->dependencyService->hasDateOverlap($dependency->commissionRule, $dependency->dependsOnRule)) {
                $analysis['date_conflicts'][] = $dependency;
            }
        });

        // Check for value conflicts
        $commissionRule->dependencies->each(function ($dependency) use (&$analysis) {
            if ($dependency->dependency_type === 'overrides' &&
                $dependency->commissionRule->value === $dependency->dependsOnRule->value) {
                $analysis['value_conflicts'][] = $dependency;
            }
        });

        return view('commission-rules.dependencies.analyze', [
            'rule' => $commissionRule,
            'analysis' => $analysis,
        ]);
    }

    /**
     * Store a newly created dependency.
     */
    public function store(CommissionRuleDependencyRequest $request, CommissionRule $commissionRule): RedirectResponse
    {
        $validated = $request->validated();

        $dependency = $this->dependencyService->addDependency(
            $commissionRule,
            CommissionRule::findOrFail($validated['depends_on_rule_id']),
            $validated['dependency_type'],
            $validated['reason']
        );

        return redirect()
            ->route('commission-rules.dependencies.index', $commissionRule)
            ->with('success', 'Dependency added successfully.');
    }

    /**
     * Remove the specified dependency.
     */
    public function destroy(CommissionRule $commissionRule, CommissionRuleDependency $dependency): RedirectResponse
    {
        if ($dependency->commission_rule_id !== $commissionRule->id) {
            abort(404);
        }

        $dependency->delete();

        return redirect()
            ->route('commission-rules.dependencies.index', $commissionRule)
            ->with('success', 'Dependency removed successfully.');
    }

    /**
     * Validate dependencies for a commission rule.
     */
    public function validate(CommissionRule $commissionRule): RedirectResponse
    {
        $isValid = $this->dependencyService->validateAndUpdateStatus($commissionRule);

        if ($isValid) {
            $message = 'All dependencies are valid.';
        } else {
            $message = 'Dependencies are invalid. Rule has been deactivated.';
        }

        return back()->with('status', $message);
    }
}
