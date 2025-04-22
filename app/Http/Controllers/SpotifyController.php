<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpotifyController extends Controller
{
    public function redirectToSpotify()
    {
        return Socialite::driver('spotify')
            ->scopes(['playlist-modify-private', 'playlist-modify-public'])
            ->redirect();
    }

    public function handleSpotifyCallback()
    {
        $user = Socialite::driver('spotify')->stateless()->user();
        Session::put('spotify_access_token', $user->token);
        Session::put('spotify_refresh_token', $user->refreshToken);
        Session::put('spotify_user', $user->id);

        return redirect('/curadoria')->with('success', 'Autenticado no Spotify!');
    }

    public static function createPlaylistWithTracks($accessToken, $userId, $trackUris, $name = 'Playlist Curada 🎶')
    {
        $playlistResponse = Http::withToken($accessToken)->post("https://api.spotify.com/v1/users/{$userId}/playlists", [
            'name' => $name,
            'description' => 'Criada com Laravel + OpenAI',
            'public' => false,
        ]);
    
        // Verificando se a criação da playlist foi bem-sucedida
        if (!$playlistResponse->successful()) {
            Log::error('Erro ao criar playlist no Spotify', ['response' => $playlistResponse->json()]);
            return null; // Ou um erro mais adequado
        }
    
        $playlist = $playlistResponse->json();
        $playlistId = $playlist['id'];
    
        // Adiciona as músicas
        $addTracksResponse = Http::withToken($accessToken)->post("https://api.spotify.com/v1/playlists/{$playlistId}/tracks", [
            'uris' => $trackUris,
        ]);
    
        // Verificando se as faixas foram adicionadas com sucesso
        if (!$addTracksResponse->successful()) {
            Log::error('Erro ao adicionar músicas à playlist no Spotify', ['response' => $addTracksResponse->json()]);
            return null; // Ou um erro mais adequado
        }
    
        return $playlist['external_urls']['spotify'] ?? null;
    }
    
}
