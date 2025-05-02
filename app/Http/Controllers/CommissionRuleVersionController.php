<?php

namespace App\Http\Controllers;

use App\Models\CommissionRule;
use App\Models\CommissionRuleVersion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CommissionRuleVersionController extends Controller
{
    /**
     * Display a listing of versions for a commission rule.
     */
    public function index(CommissionRule $commissionRule): View
    {
        $versions = $commissionRule->versions()
            ->with('creator')
            ->orderByDesc('version_number')
            ->paginate(10);

        return view('commission-rules.versions.index', [
            'rule' => $commissionRule,
            'versions' => $versions,
        ]);
    }

    /**
     * Display the specified version.
     */
    public function show(CommissionRule $commissionRule, CommissionRuleVersion $version): View
    {
        if ($version->commission_rule_id !== $commissionRule->getKey()) {
            abort(404);
        }

        return view('commission-rules.versions.show', [
            'rule' => $commissionRule,
            'version' => $version,
        ]);
    }

    /**
     * Compare two versions of a commission rule.
     */
    public function compare(
        CommissionRule $commissionRule,
        CommissionRuleVersion $versionA,
        CommissionRuleVersion $versionB
    ): View {
        if ($versionA->commission_rule_id !== $commissionRule->getKey() ||
            $versionB->commission_rule_id !== $commissionRule->getKey()) {
            abort(404);
        }

        $differences = $versionA->compareWith($versionB);

        return view('commission-rules.versions.compare', [
            'rule' => $commissionRule,
            'versionA' => $versionA,
            'versionB' => $versionB,
            'differences' => $differences,
        ]);
    }

    /**
     * Restore a previous version.
     */
    public function restore(
        Request $request,
        CommissionRule $commissionRule,
        CommissionRuleVersion $version
    ): RedirectResponse {
        if ($version->commission_rule_id !== $commissionRule->getKey()) {
            abort(404);
        }

        $request->validate([
            'change_reason' => ['required', 'string', 'max:255'],
        ]);

        if ($version->restore()) {
            // Create a new version for the restoration
            $commissionRule->createVersion(
                sprintf(
                    'Restored from version %d: %s',
                    $version->version_number,
                    $request->get('change_reason')
                )
            );

            return redirect()
                ->route('commission-rules.versions.index', $commissionRule)
                ->with('success', sprintf('Successfully restored version %d.', $version->version_number));
        }

        return back()->with('error', 'Failed to restore version.');
    }
}
