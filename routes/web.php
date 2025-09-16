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
    return view('fgdashboard');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|supervisor-timbangan-registrasi|marketing|operator-b10|supervisor-b10|audit'])->name('dashboard');

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
route::get('/cetakoutm/{id}',[ScaleController::class,'cetakoutm'])->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|supervisor-timbangan-registrasi']);
route::get('/cetakspm/{id}',[ScaleController::class,'cetakspm'])->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-registrasi|supervisor-timbangan-registrasi']);
route::any('/export',[ScaleController::class,'export_out'])->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|supervisor-timbangan-registrasi|audit']);
route::get('/cetaksegel/{id}',[ScaleController::class,'cetaksegel'])->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-registrasi|operator-b10|operator-timbangan|supervisor-timbangan-registrasi']);


Route::get('/laptim', function () {
    return view('laptim');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|supervisor-timbangan-registrasi|audit|operator-registrasi'])->name('laptim');

Route::get('/laporantimbanganmaterial', function () {
    return view('laporantimbanganmaterial');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|supervisor-timbangan-registrasi|audit'])->name('laporantimbanganmaterial');


Route::get('/createspm', function () {
    return view('createspm');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-registrasi|supervisor-timbangan-registrasi'])->name('createspm');


Route::get('/createsppb', function () {
    return view('createsppb');
})->middleware(['auth', 'verified', 'verified','role:administrator|marketing'])->name('createsppb');

Route::get('/inputkarung', function () {
    return view('inputkarung');
})->middleware(['auth', 'verified', 'verified','role:administrator|manager-logistik|operator-b10|supervisor-b10'])->name('inputkarung');

Route::get('/appavgkarung', function () {
    return view('appavgkarung');
})->middleware(['auth', 'verified', 'verified','role:administrator|manager-logistik|supervisor-b10'])->name('appavgkarung');


Route::get('/uploadappkarung', function () {
    return view('uploadappkarung');
})->middleware(['auth', 'verified', 'verified','role:administrator|manager-logistik|operator-b10|supervisor-b10'])->name('uploadappkarung');

Route::get('/uplappvkarung', function () {
    return view('uplappvkarung');
})->middleware(['auth', 'verified', 'verified','role:administrator|manager-logistik|operator-b10|supervisor-b10'])->name('uplappvkarung');


Route::get('/lapsj', function () {
    return view('lapsj');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|operator-b10|supervisor-b10|audit'])->name('lapsj');

Route::get('/sjeksesmolases', function () {
    return view('sjeksesmolases');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|operator-b10|supervisor-b10|operator-registrasi|supervisor-timbangan-registrasi'])->name('sjeksesmolases');

route::get('/cetaksj/{id}',[ScaleController::class,'cetaksj'])->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|operator-b10|supervisor-b10|audit']);
route::get('/cetaksjeksesmol/{id}',[ScaleController::class,'cetaksjeksesmol'])->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|operator-b10|supervisor-b10|operator-registrasi|supervisor-timbangan-registrasi|audit']);

route::get('/testcapture', function () {
    return view('testcapture');
});

route::get('/mastercustomer', function () {
    return view('mastercustomer');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|supervisor-timbangan-registrasi'])->name('mastercustomer');


Route::get('/timbanganmasukb19', function () {
    return view('timbanganmasukb19');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|supervisor-timbangan-registrasi'])->name('timbanganmasukb19');

Route::get('/timbanginmaterial', function () {
    return view('timbanginmaterial');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|supervisor-timbangan-registrasi'])->name('timbanginmaterial');

Route::get('/registrasimaterial', function () {
    return view('registrasimaterial');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-registrasi|supervisor-timbangan-registrasi'])->name('registrasimaterial');

route::get('/mastersupplier', function () {
    return view('mastersupplier');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|supervisor-timbangan-registrasi'])->name('mastersupplier');

route::get('/gantitgltm', function () {
    return view('gantitgltm');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|supervisor-timbangan-registrasi'])->name('gantitgltm');

Route::get('/timbangoutmaterial', function () {
    return view('timbangoutmaterial');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|supervisor-timbangan-registrasi'])->name('timbangoutmaterial');

Route::get('/fgdashboard', function () {
    return view('fgdashboard');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|supervisor-timbangan-registrasi|marketing|operator-b10|supervisor-b10|audit|operator-registrasi'])->name('fgdashboard');

Route::get('/cardwbout', function () {
    return view('cardwbout');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|supervisor-timbangan-registrasi|marketing|operator-b10|supervisor-b10|audit|operator-registrasi'])->name('cardwbout');

Route::get('/cardwbin', function () {
    return view('cardwbin');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|supervisor-timbangan-registrasi|marketing|operator-b10|supervisor-b10|audit|operator-registrasi'])->name('cardwbin');

Route::get('/cardabnormal', function () {
    return view('cardabnormal');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|supervisor-timbangan-registrasi|marketing|operator-b10|supervisor-b10|audit|operator-registrasi'])->name('cardabnormal');

Route::get('/cardloading', function () {
    return view('cardloading');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|supervisor-timbangan-registrasi|marketing|operator-b10|supervisor-b10|audit|operator-registrasi'])->name('cardloading');

Route::get('/cardantrianbesok', function () {
    return view('cardantrianbesok');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|supervisor-timbangan-registrasi|marketing|operator-b10|supervisor-b10|audit|operator-registrasi'])->name('cardantrianbesok');

Route::get('/cardantrianhariini', function () {
    return view('cardantrianhariini');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|supervisor-timbangan-registrasi|marketing|operator-b10|supervisor-b10|audit|operator-registrasi'])->name('cardantrianhariini');

Route::get('/cardregistered', function () {
    return view('cardregistered');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|supervisor-timbangan-registrasi|marketing|operator-b10|supervisor-b10|audit|operator-registrasi'])->name('cardregistered');
 

Route::get('/createpgi', function () {
    return view('createpgi');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|operator-registrasi|supervisor-timbangan-registrasi|operator-b10|supervisor-b10'])->name('createpgi');

Route::get('/ttdstruktimbangmgr', function () {
    return view('ttdstruktimbangmgr');
})->middleware(['auth', 'verified','role:administrator|manager-logistik'])->name('ttdstruktimbangmgr');

Route::get('/cardpgi', function () {
    return view('cardpgi');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|operator-registrasi|supervisor-timbangan-registrasi|marketing|operator-b10|supervisor-b10|audit'])->name('cardpgi');

Route::get('/truktransaction', function () {
    return view('truktransaction');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-timbangan|operator-registrasi|supervisor-timbangan-registrasi|marketing|operator-b10|supervisor-b10|audit'])->name('truktransaction');


Route::get('/listtmpersppb', function () {
    return view('listtmpersppb');
})->middleware(['auth', 'verified','role:administrator|manager-logistik|operator-registrasi|supervisor-timbangan-registrasi|marketing'])->name('listtmpersppb');
 



Route::get('/approvaltiketmuat', function () {
    return view('approvaltiketmuat');
})->middleware(['auth', 'verified', 'verified','role:administrator|marketing'])->name('approvaltiketmuat');

Route::get('/laptiketmuatapproved', function () {
    return view('laptiketmuatapproved');
})->middleware(['auth', 'verified', 'verified','role:administrator|manager-logistik|supervisor-b10|marketing'])->name('laptiketmuatapproved');


Route::get('/appsecurity', function () {
    return view('appsecurity');
})->middleware(['auth', 'verified', 'verified','role:administrator|manager-logistik|security'])->name('appsecurity');


Route::get('/startloading', function () {
    return view('startloading');
})->middleware(['auth', 'verified', 'verified','role:administrator|manager-logistik|operator-b10|supervisor-b10'])->name('startloading');


Route::get('/segeltruk', function () {
    return view('segeltruk');
})->middleware(['auth', 'verified', 'verified','role:administrator|manager-logistik|operator-timbangan|operator-b10|supervisor-b10'])->name('segeltruk');

route::get('/cetaktiket/{id}',[scalecontroller::class,'cetaktiket'])->name('cetaktiket');

route::post('/ttdstore',[scalecontroller::class,'ttdstore'])->name('ttdstore');


Route::get('/testingimport', function () {
    return view('testingimportmenu');
})->middleware(['auth', 'verified', 'verified','role:administrator'])->name('testingimportmenu');
route::post('/test_import',[ScaleController::class,'test_import'])->middleware(['auth', 'verified','role:administrator']);


// Route::get('/proxy-image', function () {
//     $imageUrl = "http://10.20.12.208/cgi-bin/encoder?USER=apps&PWD=Tebumas12&GET_STREAM";
//     return response()->streamDownload(function () use ($imageUrl) {
//         echo file_get_contents($imageUrl);
//     }, 'stream.jpg');
// });

require __DIR__.'/auth.php';
