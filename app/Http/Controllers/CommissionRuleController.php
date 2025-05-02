<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HandlesCommissionRuleBulkActions;
use App\Http\Requests\StoreCommissionRuleRequest;
use App\Http\Requests\UpdateCommissionRuleRequest;
use App\Models\CommissionRule;
use App\Services\CommissionRuleDependencyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CommissionRuleController extends Controller
{
    use HandlesCommissionRuleBulkActions;

    protected CommissionRuleDependencyService $dependencyService;

    public function __construct(CommissionRuleDependencyService $dependencyService)
    {
        $this->dependencyService = $dependencyService;
        $this->authorizeResource(CommissionRule::class, 'commissionRule');
    }

    /**
     * Display a listing of commission rules.
     */
    public function index(): View
    {
        $rules = CommissionRule::with(['versions', 'dependencies', 'dependents'])
            ->where('is_template', false)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $templates = CommissionRule::templates()->get();

        return view('commission-rules.index', compact('rules', 'templates'));
    }

    /**
     * Show the form for creating a new commission rule.
     */
    public function create(): View
    {
        $templates = CommissionRule::templates()->get();
        return view('commission-rules.create', compact('templates'));
    }

    /**
     * Store a newly created commission rule.
     */
    public function store(StoreCommissionRuleRequest $request): RedirectResponse
    {
        $rule = CommissionRule::create($request->validated());

        // Create initial version
        $rule->createVersion('Initial version');

        return redirect()->route('commission-rules.edit', $rule)
            ->with('success', 'Commission rule created successfully.');
    }

    /**
     * Show the form for editing the specified commission rule.
     */
    public function edit(CommissionRule $commissionRule): View
    {
        $commissionRule->load(['versions', 'dependencies.dependsOnRule', 'dependents.commissionRule']);
        return view('commission-rules.edit', compact('commissionRule'));
    }

    /**
     * Update the specified commission rule.
     */
    public function update(UpdateCommissionRuleRequest $request, CommissionRule $commissionRule): RedirectResponse
    {
        $validated = $request->validated();
        $changeReason = $request->input('change_reason');

        $commissionRule->update($validated);

        // Create new version if there are changes
        if ($commissionRule->wasChanged()) {
            $commissionRule->createVersion($changeReason);
        }

        // Update dependencies and affected rules
        $this->dependencyService->validateAndUpdateStatus($commissionRule);
        $this->dependencyService->updateAffectedRules($commissionRule);

        return redirect()->route('commission-rules.edit', $commissionRule)
            ->with('success', 'Commission rule updated successfully.');
    }

    /**
     * Remove the specified commission rule.
     */
    public function destroy(CommissionRule $commissionRule): RedirectResponse
    {
        if ($commissionRule->dependents()->exists()) {
            return back()->with('error', 'Cannot delete rule with dependent rules.');
        }

        $commissionRule->delete();

        return redirect()->route('commission-rules.index')
            ->with('success', 'Commission rule deleted successfully.');
    }

    /**
     * Duplicate the specified commission rule.
     */
    public function duplicate(CommissionRule $commissionRule): RedirectResponse
    {
        $duplicate = $commissionRule->replicate();
        $duplicate->name = $commissionRule->name . ' (Copy)';
        $duplicate->active = false;
        $duplicate->save();

        // Copy conditions and other related data
        $duplicate->conditions = $commissionRule->conditions;
        $duplicate->save();

        return redirect()->route('commission-rules.edit', $duplicate)
            ->with('success', 'Commission rule duplicated successfully.');
    }

    /**
     * Save the specified commission rule as a template.
     */
    public function saveAsTemplate(Request $request, CommissionRule $commissionRule): RedirectResponse
    {
        $request->validate([
            'template_name' => ['required', 'string', 'max:255'],
        ]);

        $template = $commissionRule->replicate();
        $template->name = $request->input('template_name');
        $template->is_template = true;
        $template->active = false;
        $template->save();

        // Copy conditions and other related data
        $template->conditions = $commissionRule->conditions;
        $template->save();

        return redirect()->route('commission-rules.edit', $template)
            ->with('success', 'Commission rule saved as template successfully.');
    }

    /**
     * Create a new commission rule from a template.
     */
    public function createFromTemplate(CommissionRule $template): RedirectResponse
    {
        if (!$template->is_template) {
            return back()->with('error', 'Selected rule is not a template.');
        }

        $rule = $template->replicate();
        $rule->name = $template->name . ' (From Template)';
        $rule->is_template = false;
        $rule->active = false;
        $rule->save();

        // Copy conditions and other related data
        $rule->conditions = $template->conditions;
        $rule->save();

        return redirect()->route('commission-rules.edit', $rule)
            ->with('success', 'Commission rule created from template successfully.');
    }
}
