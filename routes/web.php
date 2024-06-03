<?php

use App\Http\Controllers\MeetingController;
use App\Http\Controllers\ProfileController;
use App\Models\UserMeeting;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

// crear una reunión con el servicio de agora
Route::get('/crearReunion', function () {

    $meeting = Auth::User()->getUserMeetingInfo()->first();

    // Verifica si existe alguna reunión, si existe
    // alguna reunión genera el token de conexión para el usuario 
    // de lo contrario crea una reunión
    if(!isset($meeting->id))
    {
        $name = 'agora'.rand(1111,9999);
        $meetingData = createAgoraProject($name);

        //verificar si se creo el proyecto
        if(isset($meetingData->project->id))
        {
            //crear una nueva reunión
            $meeting = new UserMeeting();
            $meeting->user_id = Auth::User()->id;
            $meeting->app_id = $meetingData->project->vendor_key;
            $meeting->appCertificate = $meetingData->project->sign_key;
            $meeting->channel = $meetingData->project->name;
            $meeting->uid = rand(11111,99999);
            $meeting->save();
        }
        else
        {
            echo "El proyecto no se pudo crear";
        }
    }
    $meeting = Auth::User()->getUserMeetingInfo()->first();
    $token = createToken($meeting->app_id,$meeting->appCertificate,$meeting->channel);
    $meeting->token = $token;
    $meeting->url = generateRandomString();
    $meeting->save();
    prx($token);
})->name('crearReunion');

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
