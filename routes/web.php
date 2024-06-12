<?php

use App\Http\Controllers\MeetingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


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

Route::get('/community', function (Request $request) {
    $query = $request->input('query');
    $res = Http::get('https://apirestnodejs-jev4.onrender.com/users/mongo/view');
    $usuarios = $res->json();
    //Búsqueda de usuarios si se manda un query
    if ($query) {
        $usuarios = array_filter($usuarios, function ($user) use ($query) {
            return stripos($user['name'], $query) !== false;
        });
    }
    else
    {
        // Limitar a los 5 primeros usuarios
        $usuarios = array_slice($usuarios, 0, 5);
    }

    return view('community', ['users' => $usuarios, 'query' => $query]);
})->name('community');

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
