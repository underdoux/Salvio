<?php

namespace App\Http\Controllers;

use App\Models\CommissionRule;
use App\Models\CommissionRuleConflict;
use App\Services\CommissionRuleConflictService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommissionRuleConflictController extends Controller
{
    protected CommissionRuleConflictService $conflictService;

    public function __construct(CommissionRuleConflictService $conflictService)
    {
        $this->conflictService = $conflictService;
        $this->authorizeResource(CommissionRuleConflict::class, 'conflict');
    }

    /**
     * Display a listing of conflicts.
     */
    public function index(Request $request): View
    {
        $query = CommissionRuleConflict::with(['ruleA', 'ruleB']);

        if ($request->has('rule')) {
            $query->where(function ($q) use ($request) {
                $q->where('rule_a_id', $request->get('rule'))
                    ->orWhere('rule_b_id', $request->get('rule'));
            });
        }

        if ($request->has('status')) {
            $query->where('resolved', $request->get('status') === 'resolved');
        }

        $conflicts = $query->latest()->paginate(10);

        return view('commission-rules.conflicts.index', [
            'conflicts' => $conflicts,
        ]);
    }

    /**
     * Display the specified conflict.
     */
    public function show(CommissionRuleConflict $conflict): View
    {
        $conflict->load(['ruleA', 'ruleB']);

        $suggestions = $this->generateSuggestions($conflict);

        return view('commission-rules.conflicts.show', [
            'conflict' => $conflict,
            'suggestions' => $suggestions,
        ]);
    }

    /**
     * Resolve the specified conflict.
     */
    public function resolve(Request $request, CommissionRuleConflict $conflict): RedirectResponse
    {
        $validated = $request->validate([
            'resolution_type' => ['required', 'string', 'in:adjust_conditions,adjust_values,adjust_dates'],
            'resolution_data' => ['required', 'array'],
            'notes' => ['required', 'string', 'max:255'],
        ]);

        try {
            $this->conflictService->resolveConflict(
                $conflict,
                $validated['resolution_type'],
                $validated['resolution_data'],
                $validated['notes']
            );

            return redirect()
                ->route('commission-rules.conflicts.index')
                ->with('success', 'Conflict resolved successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to resolve conflict: ' . $e->getMessage());
        }
    }

    /**
     * Detect conflicts for all rules.
     */
    public function detectAll(): RedirectResponse
    {
        $conflicts = $this->conflictService->detectAllConflicts();

        return redirect()
            ->route('commission-rules.conflicts.index')
            ->with('success', sprintf('%d conflicts detected.', $conflicts->count()));
    }

    /**
     * Detect conflicts for a specific rule.
     */
    public function detect(CommissionRule $commissionRule): RedirectResponse
    {
        $conflicts = $this->conflictService->detectRuleConflicts($commissionRule);

        return redirect()
            ->route('commission-rules.conflicts.index', ['rule' => $commissionRule->id])
            ->with('success', sprintf('%d conflicts detected for rule "%s".', $conflicts->count(), $commissionRule->name));
    }

    /**
     * Generate resolution suggestions for a conflict.
     */
    protected function generateSuggestions(CommissionRuleConflict $conflict): array
    {
        $suggestions = [];

        switch ($conflict->conflict_type) {
            case 'value_conflict':
                $suggestions[] = [
                    'type' => 'adjust_values',
                    'description' => 'Align commission values',
                    'data' => [
                        'rule_a_value' => min($conflict->ruleA->value, $conflict->ruleB->value),
                        'rule_b_value' => min($conflict->ruleA->value, $conflict->ruleB->value),
                    ],
                ];
                break;

            case 'date_overlap':
                $suggestions[] = [
                    'type' => 'adjust_dates',
                    'description' => 'Split date ranges',
                    'data' => [
                        'rule_a_effective_from' => $conflict->ruleA->effective_from,
                        'rule_a_effective_until' => $conflict->details['date_overlap']['start'],
                        'rule_b_effective_from' => $conflict->details['date_overlap']['end'],
                        'rule_b_effective_until' => $conflict->ruleB->effective_until,
                    ],
                ];
                break;

            case 'condition_overlap':
                if (isset($conflict->details['condition_overlap']['minimum_order_amount'])) {
                    $suggestions[] = [
                        'type' => 'adjust_conditions',
                        'description' => 'Split order amount ranges',
                        'data' => [
                            'rule_a_conditions' => array_merge($conflict->ruleA->conditions ?? [], [
                                'maximum_order_amount' => $conflict->details['condition_overlap']['minimum_order_amount'],
                            ]),
                            'rule_b_conditions' => array_merge($conflict->ruleB->conditions ?? [], [
                                'minimum_order_amount' => $conflict->details['condition_overlap']['minimum_order_amount'] + 0.01,
                            ]),
                        ],
                    ];
                }
                break;
        }

        return $suggestions;
    }
}
