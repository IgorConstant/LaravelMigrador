@extends('app')

@section('title', 'Serviço de Origem')

@section('content')
    <section id="servicoOrigemBlock">
       <div class="container">
            <div class="row">
                <div class="col-md-12 col-lg-6">
                    <div class="content py-5">
                        <h1>Selecione o serviço de origem</h1>
                        <p>Aqui, você seleciona o seu serviço atual de streaming. Ao selecionar, você será redirecionado para o login. E ao realizar o login suas playlists serão recuperadas.</p>
                        <div class="grid-container">
                            <a href="{{ route('spotify.login') }}" class="logo">
                                <img src="{{ asset('images/logo-spotify.png') }}" alt="Spotify" class="img__sized">
                            </a>
                            <a href="" class="logo">
                                <img src="{{ asset('images/logo-applemusic.png') }}" alt="Apple Music" class="img__sized">
                            </a>
                            <a href="" class="logo">
                                <img src="{{ asset('images/logo-deezer.png') }}" alt="Deezer" class="img__sized">
                            </a>
                            <a href="" class="logo">
                                <img src="{{ asset('images/logo-ytmusic.png') }}" alt="YouTube Music" class="img__sized">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-6">
                    <div class="img__block">
                        <img src="{{ asset('images/undraw_audio-player_7uwh.svg') }}" alt="Serviço de Origem" class="img-fluid">
                    </div>
                </div>
            </div>
       </div>
    </section>
@endsection