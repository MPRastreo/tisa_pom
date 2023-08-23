<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Models\Event;
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

Route::get('/', fn() => redirect('login'));

Route::get('/dashboard', fn () => view('dashboard'))->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function ()
{

    // Profile Section
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Units Section
    Route::get('/units', function()
    {
        $units = Event::all();
        return view('units', compact('units'));
    })->name('units');
    Route::post('/units/events/toggle/{id}', [EventController::class, 'toggleStatus'])->name('events.toggle');
});

require __DIR__.'/auth.php';
