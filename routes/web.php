<?php

use App\Http\Controllers\ProfileController;
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
 
    return view('welcome');
});

Route::post('/extract/pdf', [App\Http\Controllers\extraction\Pdf::class, 'upload'])->name('extract.pdf');
Route::get('/extract/search-document', [App\Http\Controllers\extraction\Pdf::class, 'searchDocument'])->name('extract.search-document');
Route::get('/extract/search-user', [App\Http\Controllers\extraction\Pdf::class, 'searchUser'])->name('extract.search-user');

Route::get('/dashboard', function () {
    // phpinfo();
    // exit;
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
