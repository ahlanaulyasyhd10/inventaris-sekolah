<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MyLoanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReturnLogController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');
});

/*
|--------------------------------------------------------------------------
| Route Admin (pengelolaan penuh)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('categories', CategoryController::class)->except('show')->middleware('permission:manage-categories');
    Route::middleware('permission:manage-items')->group(function () {
        Route::get('items/export/excel', [ItemController::class, 'exportExcel'])->name('items.export.excel');
        Route::get('items/export/pdf', [ItemController::class, 'exportPdf'])->name('items.export.pdf');
        Route::get('items/import/template', [ItemController::class, 'template'])->name('items.import.template');
        Route::post('items/import', [ItemController::class, 'import'])->name('items.import');
        Route::get('items/labels', [ItemController::class, 'labels'])->name('items.labels');
        Route::delete('items/{item}/photo', [ItemController::class, 'destroyPhoto'])->name('items.photo.destroy');
        Route::resource('items', ItemController::class);
    });

    Route::get('loans/export/pdf', [LoanController::class, 'exportPdf'])->name('loans.export.pdf')->middleware('permission:export-reports');
    Route::get('loans/export/excel', [LoanController::class, 'exportExcel'])->name('loans.export.excel')->middleware('permission:export-reports');
    Route::resource('loans', LoanController::class)->except(['edit', 'update'])->middleware('permission:manage-loans');
    Route::get('loans/{loan}/receipt', [LoanController::class, 'receipt'])->name('loans.receipt')->middleware('permission:manage-loans');
    Route::patch('loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve')->middleware('permission:manage-loans');
    Route::patch('loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject')->middleware('permission:manage-loans');
    Route::get('loans/{loan}/return', [LoanController::class, 'returnForm'])->name('loans.return')->middleware('permission:manage-loans');
    Route::post('loans/{loan}/return', [LoanController::class, 'returnStore'])->name('loans.return.store')->middleware('permission:manage-loans');

    Route::get('return-logs', [ReturnLogController::class, 'index'])->name('return-logs.index')->middleware('permission:manage-loans');
    Route::patch('return-logs/{returnLog}/pay', [ReturnLogController::class, 'payFine'])->name('return-logs.pay')->middleware('permission:manage-loans');
    Route::resource('users', UserController::class)->except('show')->middleware('permission:manage-users');
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index')->middleware('permission:manage-settings');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update')->middleware('permission:manage-settings');
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index')->middleware('permission:view-activity-logs');
});

/*
|--------------------------------------------------------------------------
| Route User (guru/siswa)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index')->middleware('permission:view-catalog');
    Route::get('/my-loans', [MyLoanController::class, 'index'])->name('my-loans.index');
    Route::get('/my-loans/create', [MyLoanController::class, 'create'])->name('my-loans.create');
    Route::post('/my-loans', [MyLoanController::class, 'store'])->name('my-loans.store')->middleware('permission:request-loans');
    Route::delete('/my-loans/{loan}', [MyLoanController::class, 'destroy'])->name('my-loans.destroy');
    Route::get('/my-loans/{loan}/receipt', [MyLoanController::class, 'receipt'])->name('my-loans.receipt');
    Route::patch('/my-loans/{loan}/extend', [MyLoanController::class, 'extend'])->name('my-loans.extend')->middleware('permission:request-loans');
});

require __DIR__.'/auth.php';
