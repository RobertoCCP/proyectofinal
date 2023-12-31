<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
//agregamos los siguientes controladores
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ClienteController;
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

use Illuminate\Support\Facades\Mail;
use App\Mail\ExampleMail;

Route::get('/send-email', function () {
    $details = [
        'title' => 'Correo de prueba',
        'body' => 'Este es un correo de prueba enviado desde Laravel.',
    ];

    Mail::to('lotisour686@gmail.com')->send(new ExampleMail($details));

    return "Correo enviado";
});

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


//y creamos un grupo de rutas protegidas para los controladores
Route::group(['middleware' => ['auth']], function() {
    Route::resource('roles', RolController::class);
    Route::resource('usuarios', UsuarioController::class);
    Route::resource('clientes', ClienteController::class);
});


// Route::group(['middleware' => 'no-access'], function () {
//     // Rutas a restringir
//     Route::get('clientes', [ClienteController::class, 'index'])->name('clientes.index');
//     // Agrega aquí otras rutas a restringir
// });