<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpotifyController;
use App\Http\Controllers\YouTubeMusicController;
use App\Http\Controllers\CuradoriaController;

Route::get('/', function () {
    return view('pages/home');
});

Route::get('/servico-origem', function () {
    return view('pages/servico-origem');
});

Route::get('/playlists', [SpotifyController::class, 'playlists'])->name('playlists');

Route::get('/curadoria', [CuradoriaController::class, 'show'])->name('curadoria.show');


Route::get('/spotify/login', [SpotifyController::class, 'redirectToSpotify'])->name('spotify.login');

// Corrigido para a URL correta de callback
Route::get('/auth/spotify/callback', [SpotifyController::class, 'handleSpotifyCallback']);

Route::post('/curadoria/criar', [CuradoriaController::class, 'criar'])->name('curadoria.criar');

Route::post('/playlists/migrate', [SpotifyController::class, 'migrateSelectedPlaylists'])->name('playlists.migrate');


Route::get('/auth/youtube/redirect', [YouTubeMusicController::class, 'redirectToYouTube'])->name('youtube.login');
Route::get('/auth/youtube/callback', [YouTubeMusicController::class, 'handleYouTubeCallback']);