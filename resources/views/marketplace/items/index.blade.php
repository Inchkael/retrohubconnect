{{--
/**
 * Fichier: resources/views/marketplace/index.blade.php
--}}

@extends('layouts.layout')

@section('content')
    <div class="container" style="padding-top: 2rem; max-width: 1600px; margin: 0 auto;">
        <h1 style="color: var(--dark-color); margin-bottom: 1.5rem; text-align: center;">Marketplace - Articles</h1>

        <div style="display: flex; justify-content: center; margin-bottom: 1.5rem;">
            <a href="{{ route('marketplace.items.create') }}" class="btn-retro">
                <i class="bi bi-plus-circle-fill me-2"></i>Nouvel article
            </a>
        </div>

        <div class="row g-4">
            @forelse($items as $item)
                <div class="col-md-4">
                    <div class="service-card h-100">
                        @if($item->images->isNotEmpty())
                            <img src="{{ $item->images->first()->url }}"
                                 class="img-fluid rounded-top"
                                 alt="{{ $item->title }}"
                                 style="height: 200px; object-fit: cover; border-top-left-radius: 16px; border-top-right-radius: 16px;">
                        @endif
                        <div class="p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0" style="color: var(--dark-color);">{{ $item->title }}</h5>
                                @auth
                                    <form action="{{ route('marketplace.items.favorite', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm p-0" style="background: none; border: none;">
                                            <i class="bi {{ $item->favorites->contains('user_id', auth()->id()) ? 'bi-heart-fill text-danger' : 'bi-heart' }}" style="font-size: 1.2rem;"></i>
                                        </button>
                                    </form>
                                @endauth
                            </div>

                            <!-- Description avec option Voir plus/Voir moins -->
                            <div class="description-section mb-2">
                                <div class="description-preview">
                                    {{ Str::limit($item->description, 150) }}
                                </div>
                                @if(strlen($item->description) > 150)
                                    <div class="description-full" style="display: none; white-space: pre-line;">
                                        {{ $item->description }}
                                    </div>
                                    <div class="text-end mt-1">
                                        <button class="btn btn-link p-0 toggle-description"
                                                style="color: var(--primary-color); font-size: 0.9em; text-decoration: none;"
                                                data-item-id="{{ $item->id }}">
                                            Voir plus
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <!-- Note moyenne -->
                            @php
                                $averageRating = $item->reviews->avg('rating') ?? 0;
                                $reviewCount = $item->reviews->count();
                            @endphp
                            @if($reviewCount > 0)
                                <div class="d-flex align-items-center mb-2">
                                    <div class="text-warning me-1">
                                        @for($i = 0; $i < 5; $i++)
                                            @if($i < floor($averageRating))
                                                <i class="bi bi-star-fill"></i>
                                            @elseif($i < ceil($averageRating))
                                                <i class="bi bi-star-half"></i>
                                            @else
                                                <i class="bi bi-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <small style="color: var(--text-color); font-weight: bold;">{{ number_format($averageRating, 1) }}</small>
                                    <small style="color: var(--text-color); margin-left: 0.5rem;">({{ $reviewCount }} {{ $reviewCount > 1 ? 'avis' : 'avis' }})</small>
                                </div>
                            @else
                                <div class="d-flex align-items-center mb-2">
                                    <div class="text-warning me-1">
                                        @for($i = 0; $i < 5; $i++)
                                            <i class="bi bi-star"></i>
                                        @endfor
                                    </div>
                                    <small style="color: var(--text-color);">Aucun avis</small>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge" style="background-color: var(--tertiary-color);">
                                    {{ ucfirst($item->condition) }}
                                </span>
                                <span style="color: var(--secondary-color); font-weight: bold;">{{ $item->price }} €</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge" style="background-color: var(--primary-color);">
                                    {{ $item->category->name }}
                                </span>
                                <a href="{{ route('marketplace.items.show', $item) }}" class="btn btn-sm" style="background-color: var(--primary-color); color: white; border-radius: 8px;">
                                    Voir <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <div class="service-card p-5">
                        <i class="bi bi-exclamation-circle-fill" style="font-size: 2rem; color: var(--secondary-color); margin-bottom: 1rem;"></i>
                        <p style="color: var(--text-color); margin-bottom: 0;">Aucun article disponible.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- CSS et JavaScript -->
    <style>
        .bi-star-fill, .bi-star-half, .bi-star {
            font-size: 0.9rem;
        }
        .description-preview {
            display: block;
            overflow: hidden;
        }
        .description-full {
            display: none;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion des descriptions tronquées
            document.querySelectorAll('.toggle-description').forEach(button => {
                button.addEventListener('click', function() {
                    const container = this.closest('.description-section');
                    const preview = container.querySelector('.description-preview');
                    const full = container.querySelector('.description-full');

                    if (preview.style.display !== 'none') {
                        preview.style.display = 'none';
                        full.style.display = 'block';
                        this.textContent = 'Voir moins';
                    } else {
                        preview.style.display = 'block';
                        full.style.display = 'none';
                        this.textContent = 'Voir plus';
                    }
                });
            });
        });
    </script>
@endsection
