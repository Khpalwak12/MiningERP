<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $bindings = [
            \App\Contracts\Repositories\CustomerRepositoryInterface::class => \App\Repositories\CustomerRepository::class,
            \App\Contracts\Repositories\MarbleShipmentRepositoryInterface::class => \App\Repositories\MarbleShipmentRepository::class,
            \App\Contracts\Repositories\CustomerPaymentRepositoryInterface::class => \App\Repositories\CustomerPaymentRepository::class,
            \App\Contracts\Repositories\SankariStoneSaleRepositoryInterface::class => \App\Repositories\SankariStoneSaleRepository::class,
            \App\Contracts\Repositories\ExpenseRepositoryInterface::class => \App\Repositories\ExpenseRepository::class,
            \App\Contracts\Repositories\EmployeeRepositoryInterface::class => \App\Repositories\EmployeeRepository::class,
            \App\Contracts\Repositories\PayrollPaymentRepositoryInterface::class => \App\Repositories\PayrollPaymentRepository::class,
            \App\Contracts\Repositories\InventoryItemRepositoryInterface::class => \App\Repositories\InventoryItemRepository::class,
            \App\Contracts\Repositories\FinancialYearRepositoryInterface::class => \App\Repositories\FinancialYearRepository::class,
        ];

        foreach ($bindings as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }
    }
}
