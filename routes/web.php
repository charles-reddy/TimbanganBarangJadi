<?php

use App\Http\Controllers\CekController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScaleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/timmasuk', function () {
    return view('timmasuk');
})->middleware(['auth', 'verified'])->name('timmasuk');

Route::get('/timkeluar', function () {
    return view('timkeluar');
})->middleware(['auth', 'verified'])->name('timkeluar');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

route::get('/cek1', function (){
    return '<h1> Cek1 </h1>';
})->middleware(['auth', 'verified']);


route::get('/cek2', [CekController::class,'index'])->middleware(['auth', 'verified']);

route::get('admin', function() {
    return '<h1> Helo Admin </h1>';
})->middleware(['auth', 'verified', 'role:admin']);


route::get('penulis', function() {
    return '<h1> Helo Penulis </h1>';
})->middleware(['auth', 'verified', 'role:penulis|admin']);



route::get('tulisan', function() {
    return view('tulisan');
})->middleware(['auth', 'verified', 'role_or_permission:lihat-tulisan|admin']);

route::get('/cetakout/{id}',[ScaleController::class,'cetakout']);
route::any('/export',[ScaleController::class,'export_out']);

Route::get('/laptim', function () {
    return view('laptim');
})->middleware(['auth', 'verified'])->name('laptim');



require __DIR__.'/auth.php';
