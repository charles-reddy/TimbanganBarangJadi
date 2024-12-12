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
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|supervisor-timbangan-registrasi'])->name('timmasuk');

Route::get('/timkeluar', function () {
    return view('timkeluar');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|supervisor-timbangan-registrasi'])->name('timkeluar');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


route::get('/cetakout/{id}',[ScaleController::class,'cetakout'])->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|supervisor-timbangan-registrasi']);
route::get('/cetakspm/{id}',[ScaleController::class,'cetakspm'])->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-registrasi|supervisor-timbangan-registrasi']);
route::any('/export',[ScaleController::class,'export_out'])->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|supervisor-timbangan-registrasi']);

Route::get('/laptim', function () {
    return view('laptim');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|supervisor-timbangan-registrasi'])->name('laptim');


Route::get('/createspm', function () {
    return view('createspm');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-registrasi|supervisor-timbangan-registrasi'])->name('createspm');


Route::get('/createsppb', function () {
    return view('createsppb');
})->middleware(['auth', 'verified', 'verified','role:administrator|manager-logistik|operator-registrasi|supervisor-timbangan-registrasi'])->name('createsppb');

Route::get('/inputkarung', function () {
    return view('inputkarung');
})->middleware(['auth', 'verified', 'verified','role:administrator|manager-logistik|operator-b10|supervisor-b10'])->name('inputkarung');

Route::get('/appavgkarung', function () {
    return view('appavgkarung');
})->middleware(['auth', 'verified', 'verified','role:administrator|manager-logistik|operator-b10|supervisor-b10'])->name('appavgkarung');


Route::get('/lapsj', function () {
    return view('lapsj');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-b10|supervisor-b10'])->name('lapsj');

route::get('/cetaksj/{id}',[ScaleController::class,'cetaksj'])->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-b10|supervisor-b10']);


require __DIR__.'/auth.php';
