<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\ContractorPaymentController;
use App\Http\Controllers\ContractorProductionController;
use App\Http\Controllers\ContractorRoyaltyLedgerController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerPaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FinancialYearController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\InventoryMovementController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MineTypeController;
use App\Http\Controllers\MarbleShipmentController;
use App\Http\Controllers\MachineryItemController;
use App\Http\Controllers\MineAssetController;
use App\Http\Controllers\PayrollPaymentController;
use App\Http\Controllers\PersonalContactController;
use App\Http\Controllers\PersonalHomeExpenseController;
use App\Http\Controllers\PersonalLedgerTransactionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SankariStoneSaleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');

    Route::resource('financial-years', FinancialYearController::class)
        ->parameters(['financial-years' => 'financialYear']);
    Route::post('financial-years/{financialYear}/close', [FinancialYearController::class, 'close'])
        ->name('financial-years.close');
    Route::post('financial-years/{financialYear}/activate', [FinancialYearController::class, 'activate'])
        ->name('financial-years.activate');

    Route::resource('customers', CustomerController::class);

    Route::resource('shipments', MarbleShipmentController::class)
        ->parameters(['shipments' => 'shipment']);
    Route::resource('mine-types', MineTypeController::class)
        ->parameters(['mine-types' => 'mineType'])
        ->except(['show']);

    Route::resource('payments', CustomerPaymentController::class)
        ->parameters(['payments' => 'payment']);

    Route::resource('sankari', SankariStoneSaleController::class)
        ->parameters(['sankari' => 'sankari']);

    Route::resource('expenses', ExpenseController::class);
    Route::resource('expense-categories', ExpenseCategoryController::class)
        ->parameters(['expense-categories' => 'expenseCategory'])
        ->except(['show']);

    Route::resource('employees', EmployeeController::class);

    Route::resource('payroll', PayrollPaymentController::class)
        ->parameters(['payroll' => 'payroll']);

    Route::resource('inventory', InventoryItemController::class)
        ->parameters(['inventory' => 'inventoryItem']);

    Route::resource('mine-assets', MineAssetController::class)
        ->parameters(['mine-assets' => 'mineAsset']);
    Route::resource('machinery', MachineryItemController::class)
        ->parameters(['machinery' => 'machinery'])
        ->except(['show']);

    Route::prefix('inventory/{inventoryItem}/movements')->name('inventory.movements.')->group(function () {
        Route::get('/', [InventoryMovementController::class, 'index'])->name('index');
        Route::get('/create', [InventoryMovementController::class, 'create'])->name('create');
        Route::post('/', [InventoryMovementController::class, 'store'])->name('store');
    });

    Route::resource('journal-entries', AccountingController::class)
        ->parameters(['journal-entries' => 'journalEntry']);

    Route::get('accounting/trial-balance', [AccountingController::class, 'trialBalance'])
        ->name('accounting.trial-balance');

    Route::prefix('personal-accounts')->name('personal-accounts.')->group(function () {
        Route::resource('contacts', PersonalContactController::class);
        Route::get('contacts/{contact}/transactions/create', [PersonalLedgerTransactionController::class, 'create'])->name('contacts.transactions.create');
        Route::post('contacts/{contact}/transactions', [PersonalLedgerTransactionController::class, 'store'])->name('contacts.transactions.store');
        Route::get('transactions/{transaction}/edit', [PersonalLedgerTransactionController::class, 'edit'])->name('transactions.edit');
        Route::put('transactions/{transaction}', [PersonalLedgerTransactionController::class, 'update'])->name('transactions.update');
        Route::delete('transactions/{transaction}', [PersonalLedgerTransactionController::class, 'destroy'])->name('transactions.destroy');
        Route::resource('home-expenses', PersonalHomeExpenseController::class)
            ->parameters(['home-expenses' => 'homeExpense'])
            ->except(['show']);
    });

    Route::prefix('contractor-royalty')->name('contractor-royalty.')->group(function () {
        Route::get('/ledger', [ContractorRoyaltyLedgerController::class, 'index'])->name('ledger');
        Route::resource('productions', ContractorProductionController::class);
        Route::resource('payments', ContractorPaymentController::class)
            ->parameters(['payments' => 'payment']);
    });

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/payments', [ReportController::class, 'payments'])->name('payments');
        Route::get('/expenses', [ReportController::class, 'expenses'])->name('expenses');
        Route::get('/employees', [ReportController::class, 'employees'])->name('employees');
        Route::get('/payroll', [ReportController::class, 'payroll'])->name('payroll');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/mine-assets', [ReportController::class, 'mineAssets'])->name('mine-assets');
        Route::get('/personal-ledger', [ReportController::class, 'personalLedger'])->name('personal-ledger');
        Route::get('/personal-home-expenses', [ReportController::class, 'personalHomeExpenses'])->name('personal-home-expenses');
        Route::get('/machinery', [ReportController::class, 'machinery'])->name('machinery');
        Route::get('/daily-production', [ReportController::class, 'dailyProduction'])->name('daily-production');
        Route::get('/monthly-production', [ReportController::class, 'monthlyProduction'])->name('monthly-production');
        Route::get('/customer-balances', [ReportController::class, 'customerBalances'])->name('customer-balances');
        Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('/contractor-production', [ReportController::class, 'contractorProduction'])->name('contractor-production');
        Route::get('/contractor-payments', [ReportController::class, 'contractorPayments'])->name('contractor-payments');
        Route::get('/contractor-expenses', [ReportController::class, 'contractorExpenses'])->name('contractor-expenses');
        Route::get('/contractor-ledger', [ReportController::class, 'contractorLedger'])->name('contractor-ledger');
        Route::get('/export/{type}/excel', [ReportController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/{type}/pdf', [ReportController::class, 'exportPdf'])->name('export.pdf');
    });

    Route::resource('users', UserController::class);

    Route::resource('roles', RoleController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
