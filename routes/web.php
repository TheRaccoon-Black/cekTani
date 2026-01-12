<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BedController;
use App\Http\Controllers\LandController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\CycleLogController;
use App\Http\Controllers\CommodityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PlantingCycleController;

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

Route::get('/test', function () {
    return view('test');
});
Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard-map', [LandController::class, 'dashboardMap'])->name('dashboard.map');
Route::get('/lands/{id}/map-sectors', [LandController::class, 'mapSectors'])->name('lands.map_sectors');

Route::post('/lands/{id}/sectors', [LandController::class, 'storeSector'])->name('sectors.store');
Route::delete('/sectors/{id}', [LandController::class, 'destroySector'])->name('sectors.destroy');

Route::get('/sectors/{id}/beds', [BedController::class, 'index'])->name('sectors.beds.index');

Route::post('/sectors/{id}/beds', [BedController::class, 'store'])->name('beds.store');

Route::delete('/beds/{id}', [BedController::class, 'destroy'])->name('beds.destroy');

Route::get('/beds/{id}/edit', [BedController::class, 'edit'])->name('beds.edit');

Route::put('/beds/{id}', [BedController::class, 'update'])->name('beds.update');

Route::resource('commodities', CommodityController::class);

Route::get('/beds/{id}/history', [BedController::class, 'history'])->name('beds.history');


Route::post('/{id}/logs', [CycleLogController::class, 'store'])->name('cycles.logs.store');

Route::delete('/logs/{id}', [CycleLogController::class, 'destroy'])->name('cycles.logs.destroy');
Route::prefix('beds/{bed}')->group(function () {
    Route::get('/planting-cycles/create', [PlantingCycleController::class, 'create'])->name('cycles.create');

    Route::post('/planting-cycles', [PlantingCycleController::class, 'store'])->name('cycles.store');
});

Route::get('/finance', [TransactionController::class, 'index'])->name('finance.index');
Route::get('/finance/create', [TransactionController::class, 'create'])->name('finance.create');
Route::post('/finance', [TransactionController::class, 'store'])->name('finance.store');

Route::post('/cycles/{id}/transaction', [TransactionController::class, 'storeForCycle'])->name('cycles.transactions.store');

Route::prefix('cycles')->group(function () {
    Route::put('/{id}/harvest', [PlantingCycleController::class, 'harvest'])->name('cycles.harvest');
});

Route::get('/finance/area-report', [TransactionController::class, 'areaReport'])->name('finance.area_report');

Route::get('/analysis/comparison', [AnalysisController::class, 'comparison'])->name('analysis.comparison');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/lands/create', [LandController::class, 'create'])->name('lands.create');
    Route::get('/lands/{land}', [LandController::class, 'show'])->name('lands.show');
    Route::resource('lands', LandController::class);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
Route::get('/{id}', [PlantingCycleController::class, 'show'])->name('cycles.show');
