<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Modules\Product\Models\Product;
use App\Modules\Inventory\Models\StockLedger;
use App\Modules\Tenant\Models\Tenant;
use App\Modules\Tenant\Models\Organization;
use App\Modules\Tenant\Models\Branch;
use App\Policies\UserPolicy;
use App\Policies\ProductPolicy;
use App\Policies\StockLedgerPolicy;
use App\Policies\TenantPolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\BranchPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

/**
 * Authorization Service Provider
 * 
 * Registers all policy mappings for the application.
 * Policies enforce fine-grained RBAC/ABAC with strict tenant isolation.
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Product::class => ProductPolicy::class,
        StockLedger::class => StockLedgerPolicy::class,
        Tenant::class => TenantPolicy::class,
        Organization::class => OrganizationPolicy::class,
        Branch::class => BranchPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}

