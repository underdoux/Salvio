<?php

namespace App\Http\Controllers\Traits;

use App\Models\CommissionRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait HandlesCommissionRuleBulkActions
{
    /**
     * Handle bulk activation of commission rules.
     */
    public function bulkActivate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rules' => ['required', 'array'],
            'rules.*' => ['required', 'exists:commission_rules,id'],
        ]);

        DB::transaction(function () use ($validated) {
            CommissionRule::whereIn('id', $validated['rules'])
                ->update(['active' => true]);
        });

        return back()->with('success', 'Selected rules activated successfully.');
    }

    /**
     * Handle bulk deactivation of commission rules.
     */
    public function bulkDeactivate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rules' => ['required', 'array'],
            'rules.*' => ['required', 'exists:commission_rules,id'],
        ]);

        DB::transaction(function () use ($validated) {
            CommissionRule::whereIn('id', $validated['rules'])
                ->update(['active' => false]);
        });

        return back()->with('success', 'Selected rules deactivated successfully.');
    }

    /**
     * Handle bulk deletion of commission rules.
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rules' => ['required', 'array'],
            'rules.*' => ['required', 'exists:commission_rules,id'],
        ]);

        DB::transaction(function () use ($validated) {
            // Delete dependencies first
            DB::table('commission_rule_dependencies')
                ->whereIn('commission_rule_id', $validated['rules'])
                ->orWhereIn('depends_on_rule_id', $validated['rules'])
                ->delete();

            // Delete conflicts
            DB::table('commission_rule_conflicts')
                ->whereIn('rule_a_id', $validated['rules'])
                ->orWhereIn('rule_b_id', $validated['rules'])
                ->delete();

            // Delete versions
            DB::table('commission_rule_versions')
                ->whereIn('commission_rule_id', $validated['rules'])
                ->delete();

            // Finally, delete the rules
            CommissionRule::whereIn('id', $validated['rules'])->delete();
        });

        return back()->with('success', 'Selected rules deleted successfully.');
    }

    /**
     * Handle bulk duplication of commission rules.
     */
    public function bulkDuplicate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rules' => ['required', 'array'],
            'rules.*' => ['required', 'exists:commission_rules,id'],
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['rules'] as $ruleId) {
                $rule = CommissionRule::find($ruleId);
                if ($rule) {
                    $rule->duplicate();
                }
            }
        });

        return back()->with('success', 'Selected rules duplicated successfully.');
    }

    /**
     * Handle bulk template creation from commission rules.
     */
    public function bulkSaveAsTemplate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rules' => ['required', 'array'],
            'rules.*' => ['required', 'exists:commission_rules,id'],
            'template_prefix' => ['required', 'string', 'max:200'],
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['rules'] as $ruleId) {
                $rule = CommissionRule::find($ruleId);
                if ($rule) {
                    $rule->saveAsTemplate($validated['template_prefix'] . ' - ' . $rule->name);
                }
            }
        });

        return back()->with('success', 'Templates created successfully.');
    }
}
