<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\KundliController;
use App\Http\Controllers\PoojaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContactController;

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
    Route::get('/consultations/checkout/payment', [ConsultationController::class, 'checkout'])->name('consultations.checkout');
    Route::post('/consultations/order/place', [ConsultationController::class, 'placeOrder'])->name('consultations.order.place');

    Route::get('/kundli', [KundliController::class, 'index'])->name('kundli.index');
    Route::get('/kundli/generate', [KundliController::class, 'create'])->name('kundli.create');
    Route::post('/kundli/generate', [KundliController::class, 'store'])->name('kundli.store');
    Route::get('/kundli/checkout', [KundliController::class, 'checkout'])->name('kundli.checkout');
    Route::post('/kundli/order/place', [KundliController::class, 'placeOrder'])->name('kundli.order.place');
    Route::get('/kundli/{kundli}/download', [KundliController::class, 'download'])->name('kundli.download');
    Route::get('/kundli/{kundli}', [KundliController::class, 'show'])->name('kundli.show');

    Route::get('/horoscope-matching', [ServiceController::class, 'horoscopeMatching'])->name('horoscope.matching');
    Route::post('/horoscope-matching', [ServiceController::class, 'processMatching'])->name('horoscope.process');

    Route::get('/ask-question', [ServiceController::class, 'askQuestion'])->name('ask.question');
    Route::post('/ask-question', [ServiceController::class, 'submitQuestion'])->name('ask.submit');
    Route::get('/ask-question/checkout', [ServiceController::class, 'checkout'])->name('ask.checkout');
    Route::post('/ask-question/order/place', [ServiceController::class, 'placeOrder'])->name('ask.order.place');

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
    Route::post('/cart/update', [ProductController::class, 'updateCart'])->name('cart.update');
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
    Route::get('/questions', [DashboardController::class, 'questions'])->name('dashboard.questions');
    Route::get('/poojas', [DashboardController::class, 'poojas'])->name('dashboard.poojas');
    Route::get('/pooja/{id}', [DashboardController::class, 'poojaDetails'])->name('dashboard.pooja.details');
    Route::get('/reports', [DashboardController::class, 'reports'])->name('dashboard.reports');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('dashboard.settings');
    Route::post('/settings/preferences', [DashboardController::class, 'updatePreferences'])->name('dashboard.preferences.update');
    Route::post('/settings/profile', [DashboardController::class, 'updateProfile'])->name('dashboard.profile.update');
    Route::post('/settings/password', [DashboardController::class, 'updatePassword'])->name('dashboard.password.update');
    Route::post('/settings/profile/photo', [DashboardController::class, 'updateProfilePhoto'])->name('dashboard.profile.photo.update');
    Route::delete('/settings/profile/photo', [DashboardController::class, 'deleteProfilePhoto'])->name('dashboard.profile.photo.delete');
    Route::get('/order/{id}', [DashboardController::class, 'orderDetails'])->name('dashboard.order.details');
    Route::get('/order/{id}/track', [DashboardController::class, 'trackOrder'])->name('dashboard.order.track');
    Route::get('/order/{id}/invoice', [DashboardController::class, 'downloadInvoice'])->name('dashboard.order.invoice');
    Route::post('/order/{id}/cancel', [DashboardController::class, 'cancelOrder'])->name('dashboard.order.cancel');
});

// CMS Routes
Route::prefix('pages')->group(function () {
    Route::get('/', [App\Http\Controllers\CmsController::class, 'index'])->name('cms.index');
    Route::get('/{slug}', [App\Http\Controllers\CmsController::class, 'show'])->name('cms.show');
    Route::post('/{slug}/comment', [App\Http\Controllers\CmsController::class, 'storeComment'])->name('cms.comment.store');
});

// Testimonials Page
Route::get('/testimonials', [App\Http\Controllers\CmsController::class, 'testimonials'])->name('testimonials');

// Blogs Page
Route::get('/blogs', [App\Http\Controllers\CmsController::class, 'blogs'])->name('blogs.index');

// Contact Routes
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Captcha Route
Route::get('captcha/{config?}', '\Mews\Captcha\CaptchaController@getCaptcha')->name('captcha');

// Auth Routes - Load before dynamic pages
require __DIR__.'/auth.php';

// Dynamic List Pages
Route::get('/view/{slug}', [App\Http\Controllers\CmsController::class, 'viewListPage'])->name('list.view');

// Dynamic Pages - Must be last to avoid conflicts
Route::get('/{url}', [App\Http\Controllers\CmsController::class, 'viewDynamicPage'])->name('dynamic.view')->where('url', '^(?!admin).*');

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

    // Lists
    Route::prefix('lists')->name('lists.')->group(function () {
        Route::get('/products', [App\Http\Controllers\Admin\ListController::class, 'productLists'])->name('products');
        Route::get('/pages', [App\Http\Controllers\Admin\ListController::class, 'pageLists'])->name('pages');
        Route::get('/create/{type}', [App\Http\Controllers\Admin\ListController::class, 'create'])->name('create');
        Route::post('/store', [App\Http\Controllers\Admin\ListController::class, 'store'])->name('store');
        Route::get('/{list}/edit', [App\Http\Controllers\Admin\ListController::class, 'edit'])->name('edit');
        Route::put('/{list}', [App\Http\Controllers\Admin\ListController::class, 'update'])->name('update');
        Route::delete('/{list}', [App\Http\Controllers\Admin\ListController::class, 'destroy'])->name('delete');
        Route::get('/templates', [App\Http\Controllers\Admin\ListController::class, 'templates'])->name('templates');
        Route::delete('/templates/{template}', [App\Http\Controllers\Admin\ListController::class, 'deleteTemplate'])->name('templates.delete');
        Route::post('/preview', [App\Http\Controllers\Admin\ListController::class, 'preview'])->name('preview');
    });

    // Orders
    Route::get('/orders', [App\Http\Controllers\AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}/view', [App\Http\Controllers\AdminController::class, 'viewOrder'])->name('orders.view');
    Route::put('/orders/{id}/status', [App\Http\Controllers\AdminController::class, 'updateOrderStatus'])->name('orders.status');

    // Consultations
    Route::get('/consultations', [App\Http\Controllers\AdminController::class, 'consultations'])->name('consultations');
    Route::get('/consultations/{id}/view', [App\Http\Controllers\AdminController::class, 'viewConsultation'])->name('consultations.view');
    Route::put('/consultations/{id}/status', [App\Http\Controllers\AdminController::class, 'updateConsultationStatus'])->name('consultations.status');

    // Kundlis
    Route::get('/kundlis', [App\Http\Controllers\AdminController::class, 'kundlis'])->name('kundlis');
    Route::get('/kundlis/{id}/view', [App\Http\Controllers\AdminController::class, 'viewKundli'])->name('kundlis.view');
    Route::put('/kundlis/{id}/status', [App\Http\Controllers\AdminController::class, 'updateKundliStatus'])->name('kundlis.status');

    // Questions
    Route::get('/questions', [App\Http\Controllers\AdminController::class, 'questions'])->name('questions');
    Route::get('/questions/{id}/view', [App\Http\Controllers\AdminController::class, 'viewQuestion'])->name('questions.view');
    Route::put('/questions/{id}/status', [App\Http\Controllers\AdminController::class, 'updateQuestionStatus'])->name('questions.status');

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

    // CMS Management
    Route::get('/cms', [App\Http\Controllers\AdminController::class, 'cmsPages'])->name('cms.pages');
    Route::get('/cms/create', [App\Http\Controllers\AdminController::class, 'createCmsPage'])->name('cms.create');
    Route::post('/cms', [App\Http\Controllers\AdminController::class, 'storeCmsPage'])->name('cms.store');
    Route::get('/cms/{id}/edit', [App\Http\Controllers\AdminController::class, 'editCmsPage'])->name('cms.edit');
    Route::put('/cms/{id}', [App\Http\Controllers\AdminController::class, 'updateCmsPage'])->name('cms.update');
    Route::delete('/cms/{id}', [App\Http\Controllers\AdminController::class, 'deleteCmsPage'])->name('cms.delete');
    Route::get('/cms/page-types/{id}/check-product-fields', [App\Http\Controllers\AdminController::class, 'checkPageTypeProductFields']);
    Route::get('/cms/comments', [App\Http\Controllers\AdminController::class, 'cmsComments'])->name('cms.comments');
    Route::put('/cms/comments/{id}/approve', [App\Http\Controllers\AdminController::class, 'approveComment'])->name('cms.comments.approve');
    Route::get('/cms/categories', [App\Http\Controllers\AdminController::class, 'cmsCategories'])->name('cms.categories');
    Route::post('/cms/categories', [App\Http\Controllers\AdminController::class, 'storeCmsCategory'])->name('cms.categories.store');
    Route::put('/cms/categories/{id}', [App\Http\Controllers\AdminController::class, 'updateCmsCategory'])->name('cms.categories.update');
    Route::delete('/cms/categories/{id}', [App\Http\Controllers\AdminController::class, 'deleteCmsCategory'])->name('cms.categories.delete');
    Route::get('/cms/page-types', [App\Http\Controllers\AdminController::class, 'cmsPageTypes'])->name('cms.page-types');
    Route::get('/cms/page-types/create', [App\Http\Controllers\AdminController::class, 'createCmsPageType'])->name('cms.page-types.create');
    Route::post('/cms/page-types', [App\Http\Controllers\AdminController::class, 'storeCmsPageType'])->name('cms.page-types.store');
    Route::get('/cms/page-types/{id}/edit', [App\Http\Controllers\AdminController::class, 'editCmsPageType'])->name('cms.page-types.edit');
    Route::put('/cms/page-types/{id}', [App\Http\Controllers\AdminController::class, 'updateCmsPageType'])->name('cms.page-types.update');
    Route::delete('/cms/page-types/{id}', [App\Http\Controllers\AdminController::class, 'deleteCmsPageType'])->name('cms.page-types.delete');

    // Settings
    Route::get('/settings', [App\Http\Controllers\AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [App\Http\Controllers\AdminController::class, 'updateSettings'])->name('settings.update');

    // Contact Management
    Route::get('/contact/submissions', [App\Http\Controllers\AdminController::class, 'contactSubmissions'])->name('contact.submissions');
    Route::get('/contact/submissions/{id}', [App\Http\Controllers\AdminController::class, 'viewContactSubmission'])->name('contact.view');
    Route::delete('/contact/submissions/{id}', [App\Http\Controllers\AdminController::class, 'deleteContactSubmission'])->name('contact.delete');
    Route::get('/contact/settings', [App\Http\Controllers\AdminController::class, 'contactSettings'])->name('contact.settings');
    Route::post('/contact/settings', [App\Http\Controllers\AdminController::class, 'updateContactSettings'])->name('contact.settings.update');

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\AdminController::class, 'notifications'])->name('notifications');
    Route::get('/notifications/{id}/read', [App\Http\Controllers\AdminController::class, 'markNotificationRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\AdminController::class, 'markAllNotificationsRead'])->name('notifications.mark-all-read');

    // Footer Settings
    Route::get('/footer/settings', [App\Http\Controllers\AdminController::class, 'footerSettings'])->name('footer.settings');
    Route::post('/footer/settings', [App\Http\Controllers\AdminController::class, 'updateFooterSettings'])->name('footer.settings.update');

    // Dynamic Pages
    Route::get('/dynamic-pages', [App\Http\Controllers\AdminController::class, 'dynamicPages'])->name('dynamic-pages.index');
    Route::get('/dynamic-pages/create', [App\Http\Controllers\AdminController::class, 'createDynamicPage'])->name('dynamic-pages.create');
    Route::post('/dynamic-pages', [App\Http\Controllers\AdminController::class, 'storeDynamicPage'])->name('dynamic-pages.store');
    Route::get('/dynamic-pages/{id}/edit', [App\Http\Controllers\AdminController::class, 'editDynamicPage'])->name('dynamic-pages.edit');
    Route::put('/dynamic-pages/{id}', [App\Http\Controllers\AdminController::class, 'updateDynamicPage'])->name('dynamic-pages.update');
    Route::delete('/dynamic-pages/{id}', [App\Http\Controllers\AdminController::class, 'deleteDynamicPage'])->name('dynamic-pages.delete');

    // Media Manager
    Route::get('/media', [App\Http\Controllers\AdminController::class, 'media'])->name('media');
    Route::post('/media/upload', [App\Http\Controllers\AdminController::class, 'uploadMedia'])->name('media.upload');
    Route::delete('/media/delete', [App\Http\Controllers\AdminController::class, 'deleteMedia'])->name('media.delete');
    Route::post('/media/folder', [App\Http\Controllers\AdminController::class, 'createFolder'])->name('media.folder.create');
    Route::delete('/media/folder', [App\Http\Controllers\AdminController::class, 'deleteFolder'])->name('media.folder.delete');

    // Template Editor
    Route::prefix('template-editor')->name('template-editor.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\TemplateEditorController::class, 'index'])->name('index');
        Route::get('/{filename}/edit', [App\Http\Controllers\Admin\TemplateEditorController::class, 'edit'])->name('edit')->where('filename', '.*');
        Route::put('/{filename}', [App\Http\Controllers\Admin\TemplateEditorController::class, 'update'])->name('update')->where('filename', '.*');
        Route::post('/create', [App\Http\Controllers\Admin\TemplateEditorController::class, 'create'])->name('create');
        Route::delete('/{filename}', [App\Http\Controllers\Admin\TemplateEditorController::class, 'destroy'])->name('destroy')->where('filename', '.*');
        Route::get('/{filename}/download', [App\Http\Controllers\Admin\TemplateEditorController::class, 'download'])->name('download')->where('filename', '.*');
    });
});
