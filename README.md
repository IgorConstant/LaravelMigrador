# 🎵 LaravelMigrador
Migrador e curador de playlists entre serviços de streaming de música. Desenvolvido em Laravel, com autenticação via OAuth2, integração com as APIs do Spotify e YouTube, e funcionalidades de curadoria usando Inteligência Artificial.

![Laravel](https://img.shields.io/badge/Laravel-10.x-red)
![OAuth2](https://img.shields.io/badge/Auth-OAuth2-blue)
![Spotify](https://img.shields.io/badge/Spotify-API-green)
![YouTube](https://img.shields.io/badge/YouTube-API-red)
![AI](https://img.shields.io/badge/AI-Powered-purple)
![Status](https://img.shields.io/badge/status-em%20desenvolvimento-yellow)

## ✨ Funcionalidades

- Autenticação via **Spotify** e **YouTube** usando OAuth2
- Interface web para seleção e migração de playlists
- Criação automática de playlists no YouTube com base nas faixas do Spotify
- Busca inteligente de faixas no YouTube usando a API de busca
- **Curadoria de playlists com IA**: escolha de gêneros e anos, com sugestões geradas por IA (OpenAI)

### 🔜 Em desenvolvimento

- Integração com **Apple Music**
- Integração com **Deezer**
- Histórico de migrações e criações
- Migração reversa (YouTube → Spotify)
- Curadoria avançada com mais filtros (mood, bpm, etc)

---

## 🚀 Instalação

1. Clone o repositório:

    ```bash
    git clone https://github.com/IgorConstant/LaravelMigrador.git
    cd LaravelMigrador
    ```

2. Instale as dependências do Composer:

    ```bash
    composer install
    ```

3. Configure o arquivo `.env`:

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. Adicione as credenciais das APIs no `.env`:

    ```env
    SPOTIFY_CLIENT_ID=your_spotify_client_id
    SPOTIFY_CLIENT_SECRET=your_spotify_client_secret
    SPOTIFY_REDIRECT_URI="${APP_URL}/auth/spotify/callback"

    YOUTUBE_API_KEY=your_youtube_api_key
    YOUTUBE_CLIENT_ID=your_youtube_client_id
    YOUTUBE_CLIENT_SECRET=your_youtube_client_secret
    YOUTUBE_REDIRECT_URI="${APP_URL}/auth/youtubemusic/callback"

    DEEP_SEEK_API_KEY=your_deep_seek_api_key
    ```

5. Rode o projeto:

    ```bash
    php artisan serve
    ```

## 🧠 Tecnologias

- **Laravel**: Framework PHP para desenvolvimento web
- **OAuth2**: Protocolo de autorização para autenticação segura
- **Spotify API**: Integração com o Spotify
- **YouTube Data API v3**: Integração com o YouTube
- **DeepSeek**: Geração de playlists personalizadas com IA
- **Bootstrap**: Design responsivo

## 🧪 Requisitos

- PHP 8.1+
- Composer
- Conta de desenvolvedor no Spotify Developer Dashboard
- Projeto configurado no Google Cloud Console com APIs do YouTube habilitadas

