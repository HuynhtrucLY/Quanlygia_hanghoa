<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| WEB ROUTES
|--------------------------------------------------------------------------
*/

// 🔐 LOGIN
// ================= MẶC ĐỊNH =================
Route::get('/', function () {
    return redirect('/login');
});

// ================= AUTH =================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout']);

// ================= DASHBOARD =================
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');

// ================= PROFILE =================
Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth');
Route::post('/change-password', [AuthController::class, 'changePassword'])->middleware('auth');

// ================= USER (ADMIN) =================
Route::get('/users', function () {
    $users = \App\Models\User::all();
    return view('users.index', compact('users'));
})->middleware('auth');

Route::post('/users/approve/{id}', [AuthController::class, 'approve']);
Route::post('/users/toggle/{id}', [UserController::class, 'toggle']);

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

// ================= PRICE =================
Route::get('/prices', [PriceController::class, 'index'])->middleware('auth');
Route::get('/prices/create', [PriceController::class, 'create'])->middleware('auth');
Route::post('/prices/store', [PriceController::class, 'store'])->middleware('auth');
Route::get('/prices/edit/{id}', [PriceController::class, 'edit'])->middleware('auth');
Route::put('/prices/update/{id}', [PriceController::class, 'update'])->middleware('auth');
Route::get('/prices/compare', [PriceController::class, 'compare'])->middleware('auth');
Route::get('/prices/alert', [PriceController::class, 'alert'])->middleware('auth');


// 🏠 DASHBOARD
Route::get('/dashboard', [DashboardController::class, 'index']);
Route::get('/dashboard', [DashboardController::class, 'dashboard']);

// 📦 PRODUCT
Route::get('/products', [ProductController::class,'index']);
Route::get('/products/create', [ProductController::class,'create']);
Route::post('/products/store', [ProductController::class,'store']);
Route::get('/products/edit/{id}', [ProductController::class,'edit']);
Route::post('/products/update/{id}', [ProductController::class,'update']);
Route::get('/products/delete/{id}', [ProductController::class,'delete']);

// 🏢 SUPPLIER
Route::get('/suppliers', [SupplierController::class,'index']);
Route::get('/suppliers/create', [SupplierController::class,'create']);
Route::post('/suppliers/store', [SupplierController::class,'store']);
Route::get('/suppliers/edit/{id}', [SupplierController::class,'edit']);
Route::post('/suppliers/update/{id}', [SupplierController::class,'update']);
Route::get('/suppliers/delete/{id}', [SupplierController::class,'delete']);




Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/create', [CategoryController::class, 'create']);
Route::post('/categories/store', [CategoryController::class, 'store']);
Route::get('/categories/edit/{id}', [CategoryController::class, 'edit']);
Route::post('/categories/update/{id}', [CategoryController::class, 'update']);
Route::get('/categories/delete/{id}', [CategoryController::class, 'delete']);



Route::get('/prices/create', [PriceController::class, 'create']);
Route::post('/prices/store', [PriceController::class, 'store']);
Route::get('/prices/edit/{id}', [PriceController::class, 'edit']);
Route::put('/prices/update/{id}', [PriceController::class, 'update']);
// Thêm dấu hỏi chấm {supplierId?} để nó trở thành tham số không bắt buộc
Route::get('/prices/history/{productId}/{supplierId?}', [PriceController::class, 'getPriceHistory']);

Route::get('/prices/compare', [PriceController::class, 'compare'])
    ->name('prices.compare');

Route::get('/prices', [PriceController::class, 'index'])->name('prices.index');
Route::get('/prices/alert', [PriceController::class, 'alert'])->name('prices.alert');
Route::get('/prices/recommend', [PriceController::class, 'recommend'])
    ->name('prices.recommend');


// already imported above

Route::get('/users', [UserController::class, 'index'])->middleware('auth');
Route::post('/users/store', [UserController::class, 'store'])->middleware('auth');
Route::post('/users/delete/{id}', [UserController::class, 'delete'])->middleware('auth');
Route::post('/users/toggle/{id}', [UserController::class, 'toggle'])->middleware('auth');
Route::post('/users/reset/{id}', [UserController::class, 'reset'])->middleware('auth');
// Đường dẫn cho việc import Excel trong PriceController
Route::post('/import-excel', [PriceController::class, 'importExcel']);
Route::get('/export-history', [PriceController::class, 'exportHistory']);