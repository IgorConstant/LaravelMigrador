@extends('app')

@section('title', 'Home')

@section('content')
    <section id="heroBlock">
        <div class="px-4 py-5 my-5 text-center">
            <h1 class="display-5 fw-bold text-body-emphasis">MigradorApp</h1>
            <div class="col-lg-6 mx-auto">
                <p class="lead mb-4">Aplicação desenvolvida em Laravel, para realizar migrações e curadoria de playlists.
                </p>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a href="/servico-origem" class="btn btn-outline-secondary login_service">Primeiros Passos</a>
                </div>
            </div>
        </div>
    </section>
    <section id="howToWorks">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="img__block">
                        <img src="{{ asset('images/undraw_media-player_kxtm.svg') }}" class="img-fluid" alt="Playlist">
                    </div>
                </div>
                <div class="col-md-6 d-flex align-items-center">
                    <div class="help__text">
                        <h2>Como migrar suas playlists favoritas para outro serviço de streaming</h2>
                        <p>Quer levar suas playlists do Spotify para o YouTube Music, ou do Apple Music para o Deezer? É fácil! </p>
                        <p>Para migrar suas playlists, conecte a conta de origem (Spotify, etc.), selecione as playlists e o serviço de destino. Conecte a conta de destino para autorizar a transferência. Após isso, suas playlists serão migradas.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="curationBlock">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 d-flex align-items-center">
                    <div class="help__text">
                        <h2>Curadoria de playlists</h2>
                        <p>Quer descobrir novas músicas? Ou criar uma playlist para uma ocasião especial? </p>
                        <p>Com a curadoria de playlists, você pode descobrir novas músicas, criar playlists personalizadas e compartilhar com seus amigos.</p>
                        
                        {{-- Botao que redireciona para a pagina de curadoria --}}
                        <a href="/curadoria" class="btn btn-outline-secondary login_service">Curar Playlist</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="img__block">
                        <img src="{{ asset('images/undraw_more-music_1188.svg') }}" class="img-fluid" alt="Playlist">
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
