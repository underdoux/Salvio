<?php

namespace App\Providers;

use App\Models\CommissionRule;
use App\Models\CommissionRuleDependency;
use App\Models\Order;
use App\Models\Product;
use App\Policies\CommissionPolicy;
use App\Policies\CommissionRuleDependencyPolicy;
use App\Policies\InsightPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Product::class => ProductPolicy::class,
        Order::class => OrderPolicy::class,
        CommissionRule::class => CommissionPolicy::class,
        CommissionRuleDependency::class => CommissionRuleDependencyPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define gates for insights access
        Gate::define('view-insights', [InsightPolicy::class, 'view']);
        Gate::define('export-insights', [InsightPolicy::class, 'export']);

        // Define gates for commission rule dependencies
        Gate::define('view-dependencies', [CommissionRuleDependencyPolicy::class, 'viewAny']);
        Gate::define('view-dependency-graph', [CommissionRuleDependencyPolicy::class, 'viewGraph']);
        Gate::define('view-dependency-analysis', [CommissionRuleDependencyPolicy::class, 'viewAnalysis']);
        Gate::define('manage-dependencies', [CommissionRuleDependencyPolicy::class, 'manageBulk']);
        Gate::define('detect-conflicts', [CommissionRuleDependencyPolicy::class, 'detectConflicts']);
        Gate::define('resolve-conflicts', [CommissionRuleDependencyPolicy::class, 'resolveConflicts']);
        Gate::define('update-dependency-settings', [CommissionRuleDependencyPolicy::class, 'updateSettings']);
        Gate::define('export-dependencies', [CommissionRuleDependencyPolicy::class, 'export']);
        Gate::define('import-dependencies', [CommissionRuleDependencyPolicy::class, 'import']);
        Gate::define('view-dependency-history', [CommissionRuleDependencyPolicy::class, 'viewHistory']);
        Gate::define('restore-dependency-version', [CommissionRuleDependencyPolicy::class, 'restoreVersion']);

        // Super admin can do everything
        Gate::before(function ($user, $ability) {
            if ($user->role === 'super_admin') {
                return true;
            }
        });
    }
}
