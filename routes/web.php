<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    AdminAuthController,
    AdminDashboardController,
    AdminBookingController,
    AdminPoojaCatalogController,
    AdminOrderController,
    AdminProductController,
    AdminPujariVerificationController,
    AdminTempleController,
    AdminAstrologyController,
    AdminDigitalPoojaController,
    AdminSettlementController,
    AdminSupportController,
    AdminCmsController
};


/*
|--------------------------------------------------------------------------
| Admin Portal Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [AdminAuthController::class, 'showLoginForm'])
    ->name('admin.login');

Route::post('/login', [AdminAuthController::class, 'login'])
    ->name('admin.login.submit');

Route::post('/logout', [AdminAuthController::class, 'logout'])
    ->name('admin.logout');

// Redirect root of admin app
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});


/*
|--------------------------------------------------------------------------
| Protected Administration Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Pooja Bookings Management & Assignment
    Route::resource('bookings', AdminBookingController::class)->only(['index', 'show']);
    Route::post('bookings/{booking}/assign', [AdminBookingController::class, 'assignPujari'])->name('bookings.assign');
    Route::post('bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');
   
	// Pooja Catalog Management
    Route::resource('poojas', AdminPoojaCatalogController::class);
    Route::patch('poojas/{pooja}/toggle-status', [AdminPoojaCatalogController::class, 'toggleStatus'])->name('poojas.toggle-status');
    Route::post('pooja-categories/quick-store', [AdminPoojaCatalogController::class, 'quickStoreCategory'])->name('pooja-categories.quick-store');
    Route::post('categories/quick-store', [AdminPoojaCatalogController::class, 'quickStoreCategory'])->name('categories.quickStore');

    // E-Commerce Orders & Fulfillment
    Route::resource('orders', AdminOrderController::class)->only(['index', 'show']);
    Route::post('orders/{order}/dispatch', [AdminOrderController::class, 'dispatch'])->name('orders.dispatch');
    Route::post('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

    // Products & Inventory
    Route::resource('products', AdminProductController::class);

    // Pujari Management (Full CRUD + Status Verification)
    Route::resource('verifications/pujaris', AdminPujariVerificationController::class)
        ->names('verifications.pujaris');

    Route::post('verifications/pujaris/{pujari}/verify', [AdminPujariVerificationController::class, 'updateVerification'])
        ->name('verifications.pujaris.update-status');

    // Temples & Verification
    Route::resource('temples', AdminTempleController::class)->only(['index', 'show']);
    Route::post('temples/{temple}/verify', [AdminTempleController::class, 'verify'])->name('temples.verify');

    // Astrology Consultations
    Route::resource('astrology', AdminAstrologyController::class)->only(['index', 'show']);
    Route::post('astrology/{booking}/status', [AdminAstrologyController::class, 'updateStatus'])->name('astrology.status');

    // Digital & Live Poojas
    Route::get('digital-poojas', [AdminDigitalPoojaController::class, 'index'])->name('digital-poojas.index');
    Route::get('digital-poojas/live/{livePooja}', [AdminDigitalPoojaController::class, 'showLivePooja'])->name('digital-poojas.live');
    Route::post('digital-poojas/live/{livePooja}', [AdminDigitalPoojaController::class, 'updateSession'])->name('digital-poojas.live.update');

    // Finance, Settlements & Payouts
    Route::get('settlements', [AdminSettlementController::class, 'index'])->name('settlements.index');
    Route::post('settlements/payout/{payout}', [AdminSettlementController::class, 'executePayout'])->name('settlements.payout');

    // Customer Support Desk
    Route::resource('support', AdminSupportController::class)->only(['index', 'show']);
    Route::post('support/{ticket}/reply', [AdminSupportController::class, 'reply'])->name('support.reply');

    // CMS & Banners
    Route::get('cms/banners', [AdminCmsController::class, 'banners'])->name('cms.banners');
    Route::post('cms/banners', [AdminCmsController::class, 'storeBanner'])->name('cms.banners.store');
    Route::get('cms/blogs', [AdminCmsController::class, 'blogs'])->name('cms.blogs');
});