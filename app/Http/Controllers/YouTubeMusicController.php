<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\SpotifyController;

class YouTubeMusicController extends Controller
{
    public function redirectToYouTube()
    {
        return Socialite::driver('google')
            ->scopes(['https://www.googleapis.com/auth/youtube'])
            ->redirect();
    }

    public function handleYouTubeCallback()
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();

            if (!$user || !$user->token) {
                throw new \Exception("Token do YouTube não foi retornado.");
            }

            Session::put('youtube_access_token', $user->token);

            // Recupera playlists pendentes e chama a migração
            $playlistIds = Session::pull('pending_playlists', []);
            $spotifyAccessToken = Session::get('spotify_access_token');

            if (!empty($playlistIds)) {
                $linksMigradas = [];
                foreach ($playlistIds as $playlistId) {
                    $link = SpotifyController::migratePlaylistToYouTube($spotifyAccessToken, $playlistId, $user->token);
                    if ($link) {
                        $linksMigradas[] = $link;
                    }
                }

                return redirect()->route('playlists')
                    ->with('success', 'Playlists migradas com sucesso!')
                    ->with('links', $linksMigradas);
            }

            return redirect()->route('playlists');

        } catch (\Exception $e) {
            return redirect()->route('playlists')
                ->with('error', 'Erro ao autenticar com o YouTube: ' . $e->getMessage());
        }
    }

}