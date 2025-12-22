<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\KundliController;
use App\Http\Controllers\PoojaController;
use App\Http\Controllers\DashboardController;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');

// Language Switch
Route::get('/lang/{locale}', function ($locale) {
    $activeLanguages = \App\Models\Language::getActiveLanguages()->pluck('code')->toArray();
    if (in_array($locale, $activeLanguages)) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

// Currency Switch
Route::get('/currency/{code}', function ($code) {
    $activeCurrencies = \App\Models\Currency::getActiveCurrencies()->pluck('code')->toArray();
    if (in_array($code, $activeCurrencies)) {
        session(['currency' => $code]);
    }
    return redirect()->back();
})->name('currency.switch');

// Astrology Services
Route::prefix('services')->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/consultations', [ConsultationController::class, 'index'])->name('consultations.index');
    Route::get('/consultations/{type}', [ConsultationController::class, 'show'])->name('consultations.show');
    Route::post('/consultations/book', [ConsultationController::class, 'book'])->name('consultations.book');

    Route::get('/kundli', [KundliController::class, 'index'])->name('kundli.index');
    Route::get('/kundli/generate', [KundliController::class, 'create'])->name('kundli.create');
    Route::post('/kundli/generate', [KundliController::class, 'store'])->name('kundli.store');
    Route::get('/kundli/{kundli}', [KundliController::class, 'show'])->name('kundli.show');

    Route::get('/horoscope-matching', [ServiceController::class, 'horoscopeMatching'])->name('horoscope.matching');
    Route::post('/horoscope-matching', [ServiceController::class, 'processMatching'])->name('horoscope.process');

    Route::get('/ask-question', [ServiceController::class, 'askQuestion'])->name('ask.question');
    Route::post('/ask-question', [ServiceController::class, 'submitQuestion'])->name('ask.submit');

    Route::get('/predictions', [ServiceController::class, 'predictions'])->name('predictions.index');
    Route::post('/predictions/monthly', [ServiceController::class, 'monthlyPredictions'])->name('predictions.monthly');
    Route::post('/predictions/yearly', [ServiceController::class, 'yearlyPredictions'])->name('predictions.yearly');
});

// Pooja & Rituals
Route::prefix('pooja')->group(function () {
    Route::get('/', [PoojaController::class, 'index'])->name('pooja.index');
    Route::get('/temple', [PoojaController::class, 'temple'])->name('pooja.temple');
    Route::get('/home', [PoojaController::class, 'home'])->name('pooja.home');
    Route::get('/jaap-homam', [PoojaController::class, 'jaapHomam'])->name('pooja.jaap');
    Route::get('/special-occasion', [PoojaController::class, 'specialOccasion'])->name('pooja.special');
    Route::get('/pandit-booking', [PoojaController::class, 'panditBooking'])->name('pooja.pandit');
    Route::get('/{pooja}', [PoojaController::class, 'show'])->name('pooja.show');
    Route::post('/book', [PoojaController::class, 'book'])->name('pooja.book');
    Route::get('/checkout/payment', [PoojaController::class, 'checkout'])->name('pooja.checkout');
    Route::post('/order/place', [PoojaController::class, 'placeOrder'])->name('pooja.order.place');
});

// Shop/E-commerce
Route::prefix('shop')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('shop.index');
    Route::get('/category/{category}', [ProductController::class, 'category'])->name('shop.category');
    Route::get('/product/{id}/{slug?}', [ProductController::class, 'show'])->name('product.show');
    Route::post('/cart/add', [ProductController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/remove', [ProductController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/cart', [ProductController::class, 'cart'])->name('cart.index');
    Route::post('/checkout', [ProductController::class, 'checkout'])->name('checkout');
    Route::post('/order/place', [ProductController::class, 'placeOrder'])->name('order.place');
});

// User Dashboard
Route::middleware('auth')->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [DashboardController::class, 'orders'])->name('dashboard.orders');
    Route::get('/consultations', [DashboardController::class, 'consultations'])->name('dashboard.consultations');
    Route::get('/consultation/{id}', [DashboardController::class, 'consultationDetails'])->name('dashboard.consultation.details');
    Route::get('/consultation/{id}/reschedule', [DashboardController::class, 'rescheduleConsultation'])->name('dashboard.consultation.reschedule');
    Route::put('/consultation/{id}/reschedule', [DashboardController::class, 'updateReschedule'])->name('dashboard.consultation.reschedule.update');
    Route::post('/consultation/{id}/cancel', [DashboardController::class, 'cancelConsultation'])->name('dashboard.consultation.cancel');
    Route::get('/consultation/{id}/report', [DashboardController::class, 'downloadReport'])->name('dashboard.consultation.report');
    Route::get('/kundlis', [DashboardController::class, 'kundlis'])->name('dashboard.kundlis');
    Route::get('/poojas', [DashboardController::class, 'poojas'])->name('dashboard.poojas');
    Route::get('/pooja/{id}', [DashboardController::class, 'poojaDetails'])->name('dashboard.pooja.details');
    Route::get('/reports', [DashboardController::class, 'reports'])->name('dashboard.reports');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('dashboard.settings');
    Route::post('/settings/preferences', [DashboardController::class, 'updatePreferences'])->name('dashboard.preferences.update');
    Route::post('/settings/profile', [DashboardController::class, 'updateProfile'])->name('dashboard.profile.update');
    Route::get('/order/{id}', [DashboardController::class, 'orderDetails'])->name('dashboard.order.details');
    Route::get('/order/{id}/track', [DashboardController::class, 'trackOrder'])->name('dashboard.order.track');
    Route::get('/order/{id}/invoice', [DashboardController::class, 'downloadInvoice'])->name('dashboard.order.invoice');
    Route::post('/order/{id}/cancel', [DashboardController::class, 'cancelOrder'])->name('dashboard.order.cancel');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');

    // Products
    Route::get('/products', [App\Http\Controllers\AdminController::class, 'products'])->name('products');
    Route::get('/products/create', [App\Http\Controllers\AdminController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [App\Http\Controllers\AdminController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{product}/edit', [App\Http\Controllers\AdminController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{product}', [App\Http\Controllers\AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [App\Http\Controllers\AdminController::class, 'deleteProduct'])->name('products.delete');

    // Orders
    Route::get('/orders', [App\Http\Controllers\AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}/view', [App\Http\Controllers\AdminController::class, 'viewOrder'])->name('orders.view');
    Route::put('/orders/{id}/status', [App\Http\Controllers\AdminController::class, 'updateOrderStatus'])->name('orders.status');

    // Consultations
    Route::get('/consultations', [App\Http\Controllers\AdminController::class, 'consultations'])->name('consultations');
    Route::get('/consultations/{id}/view', [App\Http\Controllers\AdminController::class, 'viewConsultation'])->name('consultations.view');
    Route::put('/consultations/{id}/status', [App\Http\Controllers\AdminController::class, 'updateConsultationStatus'])->name('consultations.status');

    // Users
    Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('users');
    Route::put('/users/{user}/role', [App\Http\Controllers\AdminController::class, 'updateUserRole'])->name('users.role');

    // Admin Users
    Route::get('/admins', [App\Http\Controllers\AdminController::class, 'admins'])->name('admins');
    Route::post('/admins/{user}/promote', [App\Http\Controllers\AdminController::class, 'promoteToAdmin'])->name('admins.promote');
    Route::post('/admins/{user}/demote', [App\Http\Controllers\AdminController::class, 'demoteFromAdmin'])->name('admins.demote');

    // Categories
    Route::get('/categories', [App\Http\Controllers\AdminController::class, 'categories'])->name('categories');
    Route::post('/categories', [App\Http\Controllers\AdminController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{id}', [App\Http\Controllers\AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{id}', [App\Http\Controllers\AdminController::class, 'deleteCategory'])->name('categories.delete');

    // Languages
    Route::get('/languages', [App\Http\Controllers\AdminController::class, 'languages'])->name('languages');
    Route::post('/languages', [App\Http\Controllers\AdminController::class, 'storeLanguage'])->name('languages.store');
    Route::put('/languages/{id}', [App\Http\Controllers\AdminController::class, 'updateLanguage'])->name('languages.update');
    Route::post('/languages/{id}/set-default', [App\Http\Controllers\AdminController::class, 'setDefaultLanguage'])->name('languages.set-default');
    Route::delete('/languages/{id}', [App\Http\Controllers\AdminController::class, 'deleteLanguage'])->name('languages.delete');
    
    // Currencies
    Route::get('/currencies', [App\Http\Controllers\AdminController::class, 'currencies'])->name('currencies');
    Route::put('/currencies/{id}', [App\Http\Controllers\AdminController::class, 'updateCurrency'])->name('currencies.update');
    Route::post('/currencies/{id}/set-default', [App\Http\Controllers\AdminController::class, 'setDefaultCurrency'])->name('currencies.set-default');

    // Payment Gateways
    Route::get('/payment-gateways', [App\Http\Controllers\AdminController::class, 'paymentGateways'])->name('payment-gateways');
    Route::put('/payment-gateways/{id}', [App\Http\Controllers\AdminController::class, 'updatePaymentGateway'])->name('payment-gateways.update');

    // Settings
    Route::get('/settings', [App\Http\Controllers\AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [App\Http\Controllers\AdminController::class, 'updateSettings'])->name('settings.update');
});

require __DIR__.'/auth.php';
