<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TableController;






Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    // Orders
    Route::resource('orders', OrderController::class);
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::get('kitchen/display', [OrderController::class, 'kitchenDisplay'])->name('kitchen.display');

    // Tables
    Route::resource('tables', TableController::class);
    Route::patch('tables/{table}/status', [TableController::class, 'updateStatus'])->name('tables.update-status');

    // Menu Management
    Route::resource('menu-items', MenuController::class);
    Route::resource('categories', CategoryController::class);

    // Reservations
    Route::resource('reservations', ReservationController::class);
    Route::patch('reservations/{reservation}/status', [ReservationController::class, 'updateStatus']);

    // Inventory
    Route::resource('inventory', InventoryController::class);
    Route::patch('inventory/{item}/stock', [InventoryController::class, 'updateStock'])->name('inventory.update-stock');
    Route::get('inventory/low-stock/alert', [InventoryController::class, 'lowStockAlert'])->name('inventory.low-stock');

    // Payments
    Route::resource('payments', PaymentController::class);
    Route::post('payments/{order}/process', [PaymentController::class, 'processPayment'])->name('payments.process');

    // Reports
    Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('reports/staff', [ReportController::class, 'staff'])->name('reports.staff');

    // Analytics Routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/dashboard', [AnalyticsController::class, 'dashboard'])->name('dashboard');
        Route::get('/sales', [AnalyticsController::class, 'sales'])->name('sales');
        Route::get('/inventory', [AnalyticsController::class, 'inventory'])->name('inventory');
        Route::get('/staff', [AnalyticsController::class, 'staff'])->name('staff');
        Route::get('/predictive', [AnalyticsController::class, 'predictive'])->name('predictive');
        Route::get('/realtime', [AnalyticsController::class, 'realtime'])->name('realtime');
        Route::get('/export', [AnalyticsController::class, 'export'])->name('export');
    });
});

// Public routes
Route::get('menu/public', [MenuController::class, 'publicMenu'])->name('menu.public');
Route::post('reservations/public', [ReservationController::class, 'publicStore'])->name('reservations.public');


require __DIR__.'/auth.php';
