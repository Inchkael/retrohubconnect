<div class="slider-container">
    <div id="mainSlider" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            @php
                $sliders = \App\Models\Slider::getActiveOrdered();
            @endphp
            @foreach($sliders as $index => $slider)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <!-- Fond : image du slider -->
                    @if($slider->mainImage())
                        <img src="{{ $slider->mainImage()->url }}"
                             class="d-block w-100"
                             alt="{{ $slider->title }}"
                             style="height: 400px; object-fit: cover;">
                    @else
                        <div class="d-block w-100" style="height: 400px; background-color: var(--primary-color);"></div>
                    @endif

                    <!-- Superposition : texte multilingue -->
                    <div class="slide-content d-flex align-items-center justify-content-center">
                        <div class="text-center px-4">
                            <h3 class="slide-text mb-3">
                                @if($slider->title)
                                    <p class="slide-subtitle">{{ $slider->title }}</p>
                                @endif
                            </h3>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($sliders->count() > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#mainSlider" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#mainSlider" data-bs-slide="next">
                <span class="carousel-control-next" aria-hidden="true"></span>
            </button>

            <div class="carousel-indicators">
                @foreach($sliders as $index => $slider)
                    <button type="button"
                            data-bs-target="#mainSlider"
                            data-bs-slide-to="{{ $index }}"
                            class="{{ $index === 0 ? 'active' : '' }}"></button>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Bouton de modification (visible uniquement pour les admins) -->
    @auth
        @if(auth()->user()->isAdmin())
            <div class="slider-edit-button-container">
                <a href="{{ route('admin.sliders.index') }}" class="btn btn-warning btn-slider-edit" title="Gérer les sliders">
                    <i class="bi bi-pencil-square"></i>
                    <span class="button-text">Modifier le slider</span>
                </a>
            </div>
        @endif
    @endauth
</div>

<!-- Styles CSS pour le slider et le bouton de modification -->
<style>
    .slider-container {
        position: relative;
    }

    /* Conteneur du texte superposé */
    .slide-content {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5); /* Fond semi-transparent */
        color: white;
        padding: 2rem;
        text-align: center;
        font-family: 'Press Start 2P', cursive;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: fadeIn 1s ease-in;
    }

    .slide-content > div {
        max-width: 80%;
        background-color: rgba(45, 149, 213, 0.7); /* Bleu avec transparence */
        padding: 2rem;
        border-radius: 5px;
    }

    .slide-text {
        font-size: 1.8rem;
        margin-bottom: 1rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
    }

    .slide-subtitle {
        font-size: 1.2rem;
        font-family: Arial, sans-serif;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Bouton d'édition */
    .slider-edit-button-container {
        position: absolute;
        bottom: 20px;
        right: 20px;
        z-index: 1030;
    }

    .btn-slider-edit {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 8px 15px;
        border-radius: 20px;
        background-color: rgba(255, 255, 255, 0.9);
        border: 1px solid #ddd;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    .btn-slider-edit:hover {
        background-color: rgba(255, 255, 255, 1);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .btn-slider-edit i {
        margin-right: 5px;
        font-size: 1rem;
    }

    .button-text {
        font-size: 0.9rem;
        font-weight: 500;
    }

    /* Version mobile */
    @media (max-width: 767.98px) {
        .slider-edit-button-container {
            bottom: 10px;
            right: 10px;
        }

        .btn-slider-edit {
            padding: 6px 10px;
            font-size: 0.8rem;
        }

        .button-text {
            display: none;
        }

        .btn-slider-edit i {
            margin-right: 0;
            font-size: 1.1rem;
        }

        .slide-text {
            font-size: 1.2rem;
        }

        .slide-subtitle {
            font-size: 1rem;
        }
    }
</style>
