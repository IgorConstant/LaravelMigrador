<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CuradoriaController extends Controller
{
    public function criar(Request $request)
    {
        ini_set('max_execution_time', 120);
        
        // Recuperando o token de acesso e o ID do usuário do Spotify
        $accessToken = Session::get('spotify_access_token');
        $userId = Session::get('spotify_user');
    
        // Verifica se o usuário está autenticado no Spotify
        if (!$accessToken || !$userId) {
            return response()->json(['error' => 'Usuário não autenticado no Streaming.'], 401);
        }
    
        // Recuperando os gêneros e anos escolhidos
        $genres = $request->input('genres', []);
        $years = $request->input('years', []);
    
        // Verifica se pelo menos um gênero e um ano foram selecionados
        if (empty($genres) || empty($years)) {
            return response()->json(['error' => 'Selecione ao menos um gênero e ano.'], 422);
        }
    
        // Criando o prompt para o DeepSeek
        $prompt = "Liste 20 músicas populares dos gêneros: " . implode(', ', $genres) .
                  " lançadas nos anos: " . implode(', ', $years) .
                  ". Formato: Nome da música - Nome do artista.";
    
        // Chamando o DeepSeek para gerar a lista de músicas
        $deepSeekResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('DEEP_SEEK_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'deepseek/deepseek-chat:free',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => 500, // Exemplo de parâmetro adicional
        ]);
    
        // Log para verificar a resposta do DeepSeek
        Log::info('Resposta do DeepSeek:', ['response' => $deepSeekResponse->json()]);
    
        // Verificando se a resposta contém o conteúdo esperado
        if ($deepSeekResponse->successful() && isset($deepSeekResponse['choices'][0]['message']['content'])) {
            $songsText = $deepSeekResponse['choices'][0]['message']['content'];
        } else {
            // Caso o DeepSeek não retorne o conteúdo esperado
            return response()->json(['error' => 'Erro ao gerar a lista de músicas.'], 500);
        }
    
        // Separando as músicas e artistas
        $lines = explode("\n", trim($songsText));
        $tracks = [];

        foreach ($lines as $line) {
            if (strpos($line, ' - ') !== false) {
                [$songRaw, $artistRaw] = explode(' - ', $line);
                
                // Limpa o nome da música: remove numeração e markdown
                $song = preg_replace(['/^\d+\.\s*\*\*/', '/\*\*$/'], ['', ''], trim($songRaw));
                
                // Limpa o nome do artista: remove tudo que estiver entre parênteses
                $artist = preg_replace('/\s*\(.*?\)/', '', trim($artistRaw));
                
                $tracks[] = ['song' => $song, 'artist' => $artist];
            }
        }
        
    
        // Log para verificar as músicas extraídas
        Log::info('Músicas extraídas após limpeza:', ['tracks' => $tracks]);
    
        // Buscando as músicas no Spotify usando o token de acesso
        $uris = [];
        foreach ($tracks as $track) {
            // Monta o query com o formato "Nome da Música - Artista"
            $query = urlencode($track['song'] . ' - ' . strtolower($track['artist']));
            $searchUrl = "https://api.spotify.com/v1/search?q={$query}&type=track&limit=1";
        
            // Faz a requisição para buscar a música no Spotify
            $searchResponse = Http::withToken($accessToken)->get($searchUrl);
            $result = $searchResponse->json();
        
            // Se a música for encontrada, adiciona o URI; se não, ignora essa música
            if (!empty($result['tracks']['items'])) {
                $uris[] = $result['tracks']['items'][0]['uri'];
            }
        }
    
        // Caso não encontre nenhuma música no Spotify
        if (empty($uris)) {
            return response()->json(['error' => 'Nenhuma música encontrada no Spotify.'], 404);
        }
    
        // Criando a playlist com as músicas encontradas no Spotify
        $playlistUrl = SpotifyController::createPlaylistWithTracks($accessToken, $userId, $uris);
    
        // Retornando a URL da playlist criada
        return response()->json([
            'message' => 'Playlist criada com sucesso!',
            'playlist_url' => $playlistUrl,
        ]);
    }

    public function show()
    {
        $genres = [
            'Rock', 'Pop', 'Jazz', 'Hip-Hop', 'Blues', 'Country', 'Reggae', 'Samba', 'Funk', 'MPB',
            'Classical', 'Electronic', 'Metal', 'Soul', 'R&B', 'Disco', 'Folk', 'Latina Americana',
            'Reggaeton', 'K-Pop', 'Pop Punk', 'Indie', 'Sertanejo', 'Forró', 'Axé', 'Pagode', 'Samba Rock', 'Punk', 
            'Grunge', 'Britpop', 'New Wave', 'Post Hardcore', 'Emo Brasileiro', 'Rap Brasileiro'
        ];

        $years = [];
        for ($year = date('Y'); $year >= 1960; $year--) {
            $years[] = $year;
        }

        return view('pages.curadoria', compact('genres', 'years'));
    }
    
}