<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpotifyController;
use App\Http\Controllers\CuradoriaController;

Route::get('/', function () {
    return view('pages/home');
});

Route::get('/servico-origem', function () {
    return view('pages/servico-origem');
});

Route::get('/curadoria', [CuradoriaController::class, 'show'])->name('curadoria.show');


Route::get('/spotify/login', [SpotifyController::class, 'redirectToSpotify'])->name('spotify.login');

// Corrigido para a URL correta de callback
Route::get('/auth/spotify/callback', [SpotifyController::class, 'handleSpotifyCallback']);

Route::post('/curadoria/criar', [CuradoriaController::class, 'criar'])->name('curadoria.criar');
