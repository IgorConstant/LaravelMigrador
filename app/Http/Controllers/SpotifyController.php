<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Log;

class SpotifyController extends Controller
{
    public function redirectToSpotify()
    {
        return Socialite::driver('spotify')
            ->scopes(['playlist-modify-private', 'playlist-modify-public'])
            ->redirect();
    }

    public function handleSpotifyCallback(Request $request)
    {
        $user = Socialite::driver('spotify')->stateless()->user();
        Session::put('spotify_access_token', $user->token);
        Session::put('spotify_refresh_token', $user->refreshToken);
        Session::put('spotify_user', $user->id);

        // Verifica se o usuário está autenticado
        if ($user) {
            // Redireciona para a página de playlists
            return redirect()->route('playlists');
        } else {
            // Se não estiver autenticado, redireciona para a página de erro
            return redirect()->route('error');
        }
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

    public static function retrieveUserPlaylists($accessToken)
    {
        $response = Http::withToken($accessToken)->get('https://api.spotify.com/v1/me/playlists');
    
        if ($response->successful()) {
            return $response->json()['items'] ?? [];
        } else {
            Log::error('Erro ao recuperar playlists do Spotify', ['response' => $response->json()]);
            return [];
        }
    }

    public function playlists()
    {
        $accessToken = Session::get('spotify_access_token');
        if (!$accessToken) {
            return redirect()->route('spotify.login')
                ->with('error', 'Você precisa fazer login no Spotify para acessar as playlists.');
        }

        $playlists = self::retrieveUserPlaylists($accessToken);
        return view('pages.playlists', compact('playlists'));
    }


    public static function migratePlaylistToYouTube($spotifyAccessToken, $playlistId, $youtubeAccessToken)
    {
        // 1. Recupera as faixas da playlist do Spotify
        $tracksResponse = Http::withToken($spotifyAccessToken)
            ->get("https://api.spotify.com/v1/playlists/{$playlistId}");

        if (!$tracksResponse->successful()) {
            Log::error('Erro ao buscar faixas da playlist do Spotify', ['response' => $tracksResponse->json()]);
            return null;
        }

        $tracks = $tracksResponse->json()['items'] ?? [];
        $trackNames = [];
        foreach ($tracks as $item) {
            $track = $item['track'];
            $trackNames[] = $track['name'] . ' ' . implode(' ', array_column($track['artists'], 'name'));
        }

        // 2. Cria uma nova playlist no YouTube
        $playlistTitle = 'Migrada do Spotify';
        $ytPlaylistResponse = Http::withToken($youtubeAccessToken)
            ->post('https://www.googleapis.com/youtube/v3/playlists?part=snippet,status', [
                'snippet' => [
                    'title' => $playlistTitle,
                    'description' => 'Playlist migrada do Spotify',
                ],
                'status' => [
                    'privacyStatus' => 'public'
                ]
            ]);

        if (!$ytPlaylistResponse->successful()) {
            Log::error('Erro ao criar playlist no YouTube', ['response' => $ytPlaylistResponse->json()]);
            return null;
        }

        $ytPlaylistId = $ytPlaylistResponse->json()['id'];

        // 3. Para cada faixa, busca no YouTube e adiciona à playlist
        foreach ($trackNames as $trackName) {
            // Busca vídeo no YouTube
            $searchResponse = Http::withToken($youtubeAccessToken)
                ->get('https://www.googleapis.com/youtube/v3/search', [
                    'part' => 'snippet',
                    'q' => $trackName,
                    'type' => 'video',
                    'maxResults' => 1
                ]);

            if ($searchResponse->successful() && !empty($searchResponse->json()['items'])) {
                $videoId = $searchResponse->json()['items'][0]['id']['videoId'];

                // Adiciona vídeo à playlist
                Http::withToken($youtubeAccessToken)
                    ->post('https://www.googleapis.com/youtube/v3/playlistItems?part=snippet', [
                        'snippet' => [
                            'playlistId' => $ytPlaylistId,
                            'resourceId' => [
                                'kind' => 'youtube#video',
                                'videoId' => $videoId
                            ]
                        ]
                    ]);
            }
        }

        return "https://www.youtube.com/playlist?list={$ytPlaylistId}";
    }

    public function migrateSelectedPlaylists(Request $request)
    {
        $playlistIds = $request->input('playlists', []);
        $spotifyAccessToken = Session::get('spotify_access_token');
        $youtubeAccessToken = Session::get('youtube_access_token');

        // Se não estiver autenticado no YouTube, salva as playlists selecionadas e redireciona para o OAuth
        if (!$youtubeAccessToken) {
            Session::put('pending_playlists', $playlistIds);
            return redirect()->route('youtube.login');
        }

        // Se já estiver autenticado, faz a migração normalmente
        $linksMigradas = [];
        foreach ($playlistIds as $playlistId) {
            $link = self::migratePlaylistToYouTube($spotifyAccessToken, $playlistId, $youtubeAccessToken);
            if ($link) {
                $linksMigradas[] = $link;
            }
        }

        return redirect()->back()->with('success', 'Playlists migradas com sucesso!')->with('links', $linksMigradas);
    }

}
