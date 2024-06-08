<?php

use App\Http\Controllers\MeetingController;
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

Route::get('/createMeeting', function () {
    return view('createMeeting');
})->name('meeting');

// crear un token único de videollamada con el servicio de agora
Route::get('/crearTokenVideollamada', [MeetingController::class, 'createVideocallToken']);

Route::get('/joinVideocall/{url?}', [MeetingController::class, 'joinMeeting']);

Route::get('/', function () {
    return view('welcome');
});

Route::get('/datos', function () {
    $response = Http::get('https://apirestnodejs-jev4.onrender.com/users/view');
    return $response->json();
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
