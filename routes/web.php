<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Laravel\Jetstream\Http\Controllers\Inertia\ApiTokenController;
use Laravel\Jetstream\Jetstream;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia\Inertia::render('Dashboard', [
        'tokens' => request()->user()->tokens,
        'defaultPermissions' => Jetstream::$defaultPermissions,
    ]);
})->name('dashboard');

// Overwritten Jetstream
Route::group(['middleware' => config('jetstream.middleware', ['web'])], function () {
    Route::group(['middleware' => ['auth', 'verified']], function () {
        //API Routes, we do not want the page but want the api routes
        Route::post('/user/api-tokens', [ApiTokenController::class, 'store'])->name('api-tokens.store');
        Route::put('/user/api-tokens/{token}', [ApiTokenController::class, 'update'])->name('api-tokens.update');
        Route::delete('/user/api-tokens/{token}', [ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');

        Route::get('/user/profile', [ProfileController::class, 'show'])
            ->name('profile.show');
    });
});
