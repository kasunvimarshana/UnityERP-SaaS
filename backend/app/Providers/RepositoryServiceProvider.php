<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Repositories
use App\Modules\Tenant\Repositories\TenantRepositoryInterface;
use App\Modules\Tenant\Repositories\TenantRepository;
use App\Modules\Product\Repositories\ProductRepositoryInterface;
use App\Modules\Product\Repositories\ProductRepository;
use App\Modules\Inventory\Repositories\StockLedgerRepositoryInterface;
use App\Modules\Inventory\Repositories\StockLedgerRepository;

// Models (for repository injection)
use App\Modules\Tenant\Models\Tenant;
use App\Modules\Product\Models\Product;
use App\Modules\Inventory\Models\StockLedger;
use App\Modules\MasterData\Models\Currency;
use App\Modules\MasterData\Models\UnitOfMeasure;
use App\Modules\MasterData\Models\TaxRate;

// Master Data Repositories
use App\Modules\MasterData\Repositories\CurrencyRepository;
use App\Modules\MasterData\Repositories\UnitOfMeasureRepository;
use App\Modules\MasterData\Repositories\TaxRateRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Tenant Repository
        $this->app->bind(TenantRepositoryInterface::class, function ($app) {
            return new TenantRepository(new Tenant());
        });

        // Product Repository
        $this->app->bind(ProductRepositoryInterface::class, function ($app) {
            return new ProductRepository(new Product());
        });

        // Stock Ledger Repository
        $this->app->bind(StockLedgerRepositoryInterface::class, function ($app) {
            return new StockLedgerRepository(new StockLedger());
        });

        // Master Data Repositories
        $this->app->singleton(CurrencyRepository::class, function ($app) {
            return new CurrencyRepository(new Currency());
        });

        $this->app->singleton(UnitOfMeasureRepository::class, function ($app) {
            return new UnitOfMeasureRepository(new UnitOfMeasure());
        });

        $this->app->singleton(TaxRateRepository::class, function ($app) {
            return new TaxRateRepository(new TaxRate());
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
