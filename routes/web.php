<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController; 
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\CatalogController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\ReviewController as CustomerReviewController;
use App\Http\Controllers\RajaOngkirController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'landingPage'])->name('welcome');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{slug}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/philosophy', fn() => view('philosophy'))->name('philosophy');

Route::post('midtrans/callback', [MidtransWebhookController::class, 'handle']);

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'is_admin'])->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/products/{product}/json', function (App\Models\Product $product) {
        return response()->json($product->load(['category', 'variants']));
    })->name('products.json');

    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::delete('/product-images/{image}', [ProductImageController::class, 'destroy'])->name('product-images.destroy');
    
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('index');
        Route::get('/report', [AdminOrderController::class, 'report'])->name('report');
        Route::get('/export/excel', [AdminOrderController::class, 'exportExcel'])->name('exportExcel');
        Route::get('/export/csv', [AdminOrderController::class, 'export'])->name('export'); 
        Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
        Route::patch('/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('update-status');
    });

    Route::get('/reviews', [App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::patch('/reviews/{review}/approve', [App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('reviews.approve');
    Route::delete('/reviews/{review}', [App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');
});

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->name('customer.')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    Route::prefix('cart')->name('cart.')->group(function () { 
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
        Route::delete('/remove/{cartItem}', [CartController::class, 'remove'])->name('remove');
        Route::patch('/update/{cartItem}', [CartController::class, 'update'])->name('update');
    });

    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/process', [CheckoutController::class, 'process'])->name('process');
        Route::get('/finish', [CheckoutController::class, 'finish'])->name('finish');
    });

    Route::prefix('my-orders')->name('orders.')->group(function () {
        Route::get('/', [CustomerOrderController::class, 'index'])->name('index');
        Route::get('/{order_code}', [CustomerOrderController::class, 'show'])->name('show');
        Route::patch('/{order}/cancel', [CustomerOrderController::class, 'cancel'])->name('cancel');
        Route::patch('/{order}/complete', [CustomerOrderController::class, 'complete'])->name('complete');
    });

    Route::post('/reviews', [CustomerReviewController::class, 'store'])->name('reviews.store');
});

/*
|--------------------------------------------------------------------------
| RajaOngkir API Routing (Sync dengan AJAX di Checkout Page)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('api')->group(function () {
    Route::get('/provinces', [App\Http\Controllers\RajaOngkirController::class, 'getProvincesJson'])->name('api.provinces');
    Route::get('/cities/{province_id}', [App\Http\Controllers\RajaOngkirController::class, 'getCities'])->name('api.cities');
    Route::get('/districts/{city_id}', [App\Http\Controllers\RajaOngkirController::class, 'getDistricts'])->name('api.districts');
    Route::post('/check-cost', [App\Http\Controllers\RajaOngkirController::class, 'checkOngkir'])->name('api.check-cost');
});

/*
|--------------------------------------------------------------------------
| Profile & Logic
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/redirect-after-login', function () {
    return Auth::user()->role === 'admin' 
        ? redirect()->route('admin.dashboard') 
        : redirect()->route('customer.home');
})->middleware('auth')->name('dashboard');

require __DIR__.'/auth.php';