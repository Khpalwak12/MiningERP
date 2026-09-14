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
            \App\Contracts\Repositories\ExpenseCategoryRepositoryInterface::class => \App\Repositories\ExpenseCategoryRepository::class,
            \App\Contracts\Repositories\MineTypeRepositoryInterface::class => \App\Repositories\MineTypeRepository::class,
            \App\Contracts\Repositories\StoneTypeRepositoryInterface::class => \App\Repositories\StoneTypeRepository::class,
            \App\Contracts\Repositories\EmployeeRepositoryInterface::class => \App\Repositories\EmployeeRepository::class,
            \App\Contracts\Repositories\PayrollPaymentRepositoryInterface::class => \App\Repositories\PayrollPaymentRepository::class,
            \App\Contracts\Repositories\InventoryItemRepositoryInterface::class => \App\Repositories\InventoryItemRepository::class,
            \App\Contracts\Repositories\FinancialYearRepositoryInterface::class => \App\Repositories\FinancialYearRepository::class,
            \App\Contracts\Repositories\ContractorProductionRepositoryInterface::class => \App\Repositories\ContractorProductionRepository::class,
            \App\Contracts\Repositories\ContractorPaymentRepositoryInterface::class => \App\Repositories\ContractorPaymentRepository::class,
            \App\Contracts\Repositories\MachineryItemRepositoryInterface::class => \App\Repositories\MachineryItemRepository::class,
            \App\Contracts\Repositories\MineAssetRepositoryInterface::class => \App\Repositories\MineAssetRepository::class,
            \App\Contracts\Repositories\PersonalContactRepositoryInterface::class => \App\Repositories\PersonalContactRepository::class,
            \App\Contracts\Repositories\PersonalLedgerTransactionRepositoryInterface::class => \App\Repositories\PersonalLedgerTransactionRepository::class,
            \App\Contracts\Repositories\PersonalHomeExpenseRepositoryInterface::class => \App\Repositories\PersonalHomeExpenseRepository::class,
        ];

        foreach ($bindings as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }
    }
}
