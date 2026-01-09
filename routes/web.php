<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BedController;
use App\Http\Controllers\LandController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommodityController;

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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard-map', [LandController::class, 'dashboardMap'])->name('dashboard.map');
Route::get('/lands/{id}/map-sectors', [LandController::class, 'mapSectors'])->name('lands.map_sectors');

Route::post('/lands/{id}/sectors', [LandController::class, 'storeSector'])->name('sectors.store');
Route::delete('/sectors/{id}', [LandController::class, 'destroySector'])->name('sectors.destroy');

// URL: /sectors/1/beds
Route::get('/sectors/{id}/beds', [BedController::class, 'index'])->name('sectors.beds.index');

// 2. Menyimpan bedengan baru ke dalam sektor
// URL: /sectors/1/beds (Method: POST)
Route::post('/sectors/{id}/beds', [BedController::class, 'store'])->name('beds.store');

// 3. Menghapus bedengan
// URL: /beds/5 (Method: DELETE)
Route::delete('/beds/{id}', [BedController::class, 'destroy'])->name('beds.destroy');

// Form Edit Bedengan
Route::get('/beds/{id}/edit', [BedController::class, 'edit'])->name('beds.edit');

// Proses Simpan Perubahan (Update)
Route::put('/beds/{id}', [BedController::class, 'update'])->name('beds.update');

Route::resource('commodities', CommodityController::class);

Route::middleware('auth')->group(function () {
    Route::get('/lands/create', [LandController::class, 'create'])->name('lands.create');
    Route::get('/lands/{land}', [LandController::class, 'show'])->name('lands.show');
    Route::resource('lands', LandController::class);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
