@extends('app')

@section('title', 'Playlists')

@section('content')
    <section id="playlistsRetrieve" class="py-5">
        <div class="container">
            <div class="title-block">
                <h1 class="text-center mb-0">Suas Playlists</h1>
                <p class="text-center">Logo abaixo estão suas playlists. Selecione as que deseja migrar e, em seguida,
                    escolha o serviço de destino.</p>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="gray__box my-4">
                        <form action="" method="POST">
                            @csrf
                            @if(count($playlists) > 0)
                                <ul class="list-unstyled">
                                    @foreach($playlists as $playlist)
                                        <li class="media mb-3 align-items-center d-flex">
                                            <input type="checkbox" name="playlists[]" value="{{ $playlist['id'] }}" style="margin-right: 10px">
                                            {{-- Verifica se a playlist tem imagem --}}
                                            @if(isset($playlist['images'][0]))
                                                <img src="{{ $playlist['images'][0]['url'] }}" alt="{{ $playlist['name'] }}"
                                                    style="width: 50px; height: 50px;margin-right: 10px">
                                            @else
                                                <img src="https://via.placeholder.com/50" alt="Sem imagem">
                                            @endif
                                            <div class="media-body mr-2">
                                                <h6 class="mt-0 mb-1">{{ $playlist['name'] }}</h6>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                <button type="submit" class="btn btn-primary">Migrar Playlists</button>
                            @else
                                <p>Nenhuma playlist encontrada.</p>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection