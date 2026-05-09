<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController; 
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\CatalogController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\CartController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Akses Tanpa Login)
|--------------------------------------------------------------------------
*/

// Halaman Landing Utama
Route::get('/', [HomeController::class, 'landingPage'])->name('welcome');

// Rute Katalog & Collections (Filter via parameter ?collection=)
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{slug}', [CatalogController::class, 'show'])->name('catalog.show');

// Halaman Filosofi
Route::get('/philosophy', function () {
    return view('philosophy');
})->name('philosophy');

// Webhook Midtrans
Route::post('midtrans/callback', [MidtransWebhookController::class, 'handle']);


/*
|--------------------------------------------------------------------------
| Auth Routes (Login, Register, Logout)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout'); 


/*
|--------------------------------------------------------------------------
| Admin Routes (Hanya untuk Role Admin)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware(['auth', 'is_admin'])
    ->name('admin.')
    ->group(function () {
        
        // Dashboard Admin
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        
        // Manajemen Produk & Kategori
        Route::resource('products', ProductController::class);
        Route::resource('categories', CategoryController::class);
        
        // Manajemen Gambar Produk (AJAX)
        Route::delete('/product-images/{image}', [ProductImageController::class, 'destroy'])
            ->name('product-images.destroy');
        
        // Manajemen Pesanan
        Route::get('orders',                  [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}',          [OrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::get('orders/export',           [OrderController::class, 'export'])->name('orders.export');
    });


/*
|--------------------------------------------------------------------------
| Customer Routes (Wajib Login untuk Transaksi)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->name('customer.')->group(function () {
    
    // Dashboard User
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    // Keranjang Belanja
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
    Route::patch('/cart/update/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    
    // Checkout & Pembayaran
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/finish', [CheckoutController::class, 'finish'])->name('payment.finish');
});


/*
|--------------------------------------------------------------------------
| Profile & Redirect Logic
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Logika Pengalihan Setelah Login
Route::get('/redirect-after-login', function () {
    $user = Auth::user();
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('customer.home');
})->middleware('auth')->name('dashboard');

require __DIR__.'/auth.php';