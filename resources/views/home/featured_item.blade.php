<!--
====================================================================================================
FICHIER : resources/views/home/featured_item.blade.php
----------------------------------------------------------------------------------------------------
Description : Section mettant en avant un article spécial (item) sur la page d'accueil.
              Affiche l'image, le titre, la description, le prix et un bouton pour voir les détails.
----------------------------------------------------------------------------------------------------
-->

@if($featuredItem)
    <div class="container section-container mt-5">
        <div class="section-header d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title">
                <i class="fas fa-star me-2"></i> {{ __('Article à la une') }}
            </h2>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card featured-card">
                    <div class="row g-0">
                        <!-- Image de l'article -->
                        <div class="col-md-4">
                            <img src="{{ $featuredItem->image_url ?? asset('images/default-item.png') }}"
                                 alt="{{ $featuredItem->name }}"
                                 class="img-fluid rounded-start h-100"
                                 style="object-fit: cover; height: 200px;">
                        </div>
                        <!-- Contenu de l'article -->
                        <div class="col-md-8">
                            <div class="card-body d-flex flex-column h-100">
                                <h3 class="card-title mb-2">{{ $featuredItem->name }}</h3>
                                <p class="card-text text-muted flex-grow-1">
                                    {{ Str::limit($featuredItem->description, 150) }}
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                    <span class="badge bg-retro me-2">
                                        <i class="fas fa-tag me-1"></i> {{ $featuredItem->price }} €
                                    </span>
                                        @if($featuredItem->condition)
                                            <span class="badge bg-retro">
                                            <i class="fas fa-circle me-1"></i> {{ ucfirst($featuredItem->condition) }}
                                        </span>
                                        @endif
                                    </div>
                                    <a href="{{ route('items.show', $featuredItem) }}"
                                       class="btn btn-retro">
                                        <i class="fas fa-eye me-1"></i> {{ __('Voir les détails') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
