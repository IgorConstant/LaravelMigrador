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
            </div>
        </div>
    </section>
    <section id="curadoriaSection">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="slider-container">
                        <p class="text-center">Selecione os anos, para curadoria.</p>
                        <div id="slider"></div>
                        <div class="years-label">
                            <span id="year-min">{{ $years[0] }}</span>
                            <span id="year-max">{{ $years[count($years) - 1] }}</span>
                        </div>
                    </div>
                    <div class="genres-container justify-content-center">
                        @foreach ($genres as $genre)
                            <button class="genre-btn" data-genre="{{ $genre }}">{{ $genre }}</button>
                        @endforeach
                    </div>

                    <div class="btn__block">
                        <button id="curarPlaylist" class="btn login_service mt-4 mb-4 centered-btn">Curar Playlist</button>
                    </div>
                </div>
            </div>

            <div id="playlistCreatedModal" style="z-index: 999; display:none; position:fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5);">
                <div style="position: relative; margin: 10% auto; padding: 20px; width: 90%; max-width: 600px; background: #fff; border-radius: 4px;">
                    <h2 class="text-center">Playlist Criada</h2>
                    <p class="text-center" id="modalMessage">Sua playlist foi criada com sucesso!</p>
                    <div style="text-align:center;">
                        <button id="closeModal" class="btn login_service my-3">Fechar</button>
                    </div>
                </div>
            </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let selectedGenres = [];
            let selectedYears = [];

            const slider = document.getElementById('slider');
            noUiSlider.create(slider, {
                start: [1960, 2025],
                connect: true,
                step: 1,
                range: {
                    'min': 1960,
                    'max': 2025
                },
                format: {
                    to: v => Math.round(v),
                    from: v => Number(v)
                }
            });

            const yearMin = document.getElementById('year-min');
            const yearMax = document.getElementById('year-max');

            slider.noUiSlider.on('update', function (values) {
                const startYear = Number(values[0]);
                const endYear = Number(values[1]);
                yearMin.textContent = startYear;
                yearMax.textContent = endYear;
                selectedYears = [];
                for (let y = startYear; y <= endYear; y++) {
                    selectedYears.push(y);
                }
            });

            const genreBtns = document.querySelectorAll('.genre-btn');
            genreBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const genre = btn.dataset.genre;
                    btn.classList.toggle('active');
                    if (selectedGenres.includes(genre)) {
                        selectedGenres = selectedGenres.filter(g => g !== genre);
                    } else {
                        selectedGenres.push(genre);
                    }
                });
            });

            const curarPlaylistBtn = document.getElementById('curarPlaylist');
            curarPlaylistBtn.addEventListener('click', () => {
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
                            const modal = document.getElementById('playlistCreatedModal');
                            const modalMessage = document.getElementById('modalMessage');
                            modalMessage.innerHTML = 'Acesse: <a href="' + data.playlist_url + '" target="_blank">' + 'Link da Playlist' + '</a>';
                            modal.style.display = 'block';
                            const closeModal = document.getElementById('closeModal');
                            closeModal.onclick = function () {
                                modal.style.display = 'none';
                                window.open(data.playlist_url, '_blank');
                            };
                        
                        } else {
                            alert('Erro ao criar playlist.');
                        }
                    });
            });
        });
    </script>
@endsection