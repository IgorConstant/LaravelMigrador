{{-- filepath: /Users/igorconstant/Workspace/MigradorApp/resources/views/pages/curadoria.blade.php --}}
@extends('app')

@section('title', 'Curadoria')

@section('content')
    <section id="heroBlock">
        <div class="px-4 my-5 text-center">
            <h1 class="display-5 fw-bold text-body-emphasis">Curadoria</h1>
            <div class="col-lg-6 mx-auto">
                <p class="lead mb-4">Com apenas alguns cliques, você cria a playlist perfeita! Selecione o gênero e o ano, e
                    nós cuidamos do resto, gerando uma lista personalizada no seu serviço de streaming.</p>
                    
                {{-- Button para realizar login na plataforma --}}
                @if (!Auth::check())
                    <a href="/spotify/login" class="btn btn-outline-secondary login_service">Login no Streaming</a>
                @endif
            </div>
        </div>
    </section>
    <section id="curadoriaSection">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="selectors">
                        <div class="genres">
                            @foreach ($genres as $genre)
                                <button class="genre-btn" data-genre="{{ $genre }}">{{ $genre }}</button>
                            @endforeach
                        </div>

                        <br>

                        <div class="years">
                            @foreach ($years as $year)
                                <button class="year-btn" data-year="{{ $year }}">{{ $year }}</button>
                            @endforeach
                        </div>
                    </div>  
                </div>
            </div>
    </section>
@endsection
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let selectedGenres = [];
        let selectedYears = [];

        document.querySelectorAll('.genre-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const genre = btn.dataset.genre;
                btn.classList.toggle('selected');
                if (selectedGenres.includes(genre)) {
                    selectedGenres = selectedGenres.filter(g => g !== genre);
                } else {
                    selectedGenres.push(genre);
                }
            });
        });

        document.querySelectorAll('.year-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const year = btn.dataset.year;
                btn.classList.toggle('selected');
                if (selectedYears.includes(year)) {
                    selectedYears = selectedYears.filter(y => y !== year);
                } else {
                    selectedYears.push(year);
                }
            });
        });

        // Botão para criar playlist
        const criarBtn = document.createElement('button');
        criarBtn.innerText = "Criar Playlist 🎵";
        criarBtn.classList.add('btn', 'login_service', 'mt-4', 'mb-4', 'centered-btn');
        criarBtn.addEventListener('click', () => {
            fetch('/curadoria/criar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    genres: selectedGenres,
                    years: selectedYears
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.playlist_url) {
                        alert('Playlist criada! Acesse: ' + data.playlist_url);
                        window.open(data.playlist_url, '_blank');
                    } else {
                        alert('Erro ao criar playlist.');
                    }
                });
        });

        document.querySelector('#curadoriaSection .container').appendChild(criarBtn);
    });
</script>