<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Event Service Provider
 * 
 * Registers all event-listener mappings for the application.
 * Implements event-driven architecture for asynchronous workflows.
 * All events and listeners are tenant-aware and queued for performance.
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Product Events
        \App\Events\Product\ProductCreated::class => [],
        \App\Events\Product\ProductUpdated::class => [],
        \App\Events\Product\ProductDeleted::class => [],

        // Inventory Events
        \App\Events\Inventory\StockMovementRecorded::class => [],
        
        \App\Events\Inventory\LowStockDetected::class => [
            \App\Listeners\Inventory\SendLowStockNotification::class,
        ],
        
        \App\Events\Inventory\StockExpiring::class => [
            \App\Listeners\Inventory\SendStockExpiryAlert::class,
        ],

        // Sales Events
        \App\Events\Sales\OrderCreated::class => [
            \App\Listeners\CRM\UpdateCustomerStatistics::class,
        ],
        
        \App\Events\Sales\OrderApproved::class => [
            \App\Listeners\Sales\GenerateInvoiceFromOrder::class,
        ],
        
        \App\Events\Sales\OrderFulfilled::class => [
            \App\Listeners\Sales\UpdateInventoryOnSale::class,
        ],

        // Invoice Events
        \App\Events\Invoice\InvoiceGenerated::class => [
            \App\Listeners\Invoice\SendInvoiceToCustomer::class,
        ],
        
        \App\Events\Invoice\InvoicePaymentReceived::class => [
            \App\Listeners\Invoice\SendPaymentConfirmation::class,
            \App\Listeners\CRM\UpdateCustomerStatistics::class,
        ],
        
        \App\Events\Invoice\InvoiceOverdue::class => [],

        // CRM Events
        \App\Events\CRM\CustomerCreated::class => [
            \App\Listeners\CRM\UpdateCustomerStatistics::class,
        ],
        
        \App\Events\CRM\LeadConverted::class => [],

        // Procurement Events
        \App\Events\Procurement\PurchaseOrderApproved::class => [
            \App\Listeners\Procurement\NotifyPurchaseOrderApproval::class,
        ],
        
        \App\Events\Procurement\GoodsReceived::class => [],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
