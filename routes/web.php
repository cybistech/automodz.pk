<?php

use App\Http\Controllers\FeedController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ShippingCityController as AdminShippingCityController;
use App\Http\Controllers\Payment\JazzCashController;
use App\Http\Controllers\Payment\StripeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\GuestOrderController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\OrderController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Shop\ProductReviewController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/uploads/{path}', function (string $path) {
    abort_unless(Storage::disk('public')->exists($path), 404);

    return response()->file(Storage::disk('public')->path($path), [
        'Cache-Control' => 'public, max-age=31536000, immutable',
    ]);
})->where('path', '.*')->name('uploads.public');

Route::get('/feed.xml', [FeedController::class, 'products'])->name('feed.products');

Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');
Route::get('/llms.txt', [SitemapController::class, 'llms'])->name('llms');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap-pages.xml', [SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('/sitemap-categories.xml', [SitemapController::class, 'categories'])->name('sitemap.categories');
Route::get('/sitemap-products.xml', [SitemapController::class, 'products'])->name('sitemap.products');
Route::get('/sitemap', [SitemapController::class, 'html'])->name('sitemap.html');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::post('/products/{slug}/reviews', [ProductReviewController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('products.reviews.store');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{productId}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{productId}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

Route::get('/order/track', [GuestOrderController::class, 'trackForm'])->name('orders.track');
Route::post('/order/track', [GuestOrderController::class, 'track'])->name('orders.track.submit');

Route::get('/order/confirmation/{order}', [OrderController::class, 'confirmation'])->name('orders.confirmation');
Route::get('/payment/stripe/success/{order}', [StripeController::class, 'success'])->name('payment.stripe.success');
Route::get('/payment/stripe/cancel/{order}', [StripeController::class, 'cancel'])->name('payment.stripe.cancel');
Route::post('/payment/jazzcash/return', [JazzCashController::class, 'return'])->name('payment.jazzcash.return');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('orders.index');
    })->name('dashboard');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', AdminCategoryController::class)->except(['show']);
    Route::resource('shipping-cities', AdminShippingCityController::class)->except(['show']);
    Route::resource('products', AdminProductController::class)->except(['show']);
    Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::patch('reviews/{review}', [AdminReviewController::class, 'update'])->name('reviews.update');
    Route::delete('reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
});

require __DIR__.'/auth.php';
