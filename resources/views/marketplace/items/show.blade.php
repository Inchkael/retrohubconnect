@extends('layouts.layout')

@section('content')
    <div class="container" style="padding-top: 2rem; max-width: 1000px; margin: 0 auto;">
        <div class="service-card p-4">
            <!-- En-tête avec titre et bouton favoris -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 style="color: var(--dark-color); margin-bottom: 0;">{{ $item->title }}</h1>
                @auth
                    <form action="{{ route('marketplace.items.favorite', $item) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn" style="background-color: {{ $item->favorites->contains('user_id', auth()->id()) ? '#dc3545' : 'var(--secondary-color)' }}; color: white; border-radius: 8px;">
                            <i class="bi {{ $item->favorites->contains('user_id', auth()->id()) ? 'bi-heart-fill' : 'bi-heart' }} me-1"></i>
                            {{ $item->favorites->contains('user_id', auth()->id()) ? 'Retirer des favoris' : 'Ajouter aux favoris' }}
                        </button>
                    </form>
                @endauth
            </div>

            <!-- Prix et état -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge" style="background-color: var(--tertiary-color);">
                    {{ ucfirst($item->condition) }}
                </span>
                <span style="color: var(--secondary-color); font-weight: bold; font-size: 1.2rem;">
                    {{ number_format($item->price, 2) }} €
                </span>
            </div>

            <!-- Description avec option Voir plus/Voir moins -->
            <div class="description-section mb-3">
                <div class="description-preview" style="max-height: 4.8em; overflow: hidden; white-space: pre-line;">
                    {{ Str::limit($item->description, 200) }}
                </div>
                @if(strlen($item->description) > 200)
                    <div class="description-full" style="display: none; white-space: pre-line;">
                        {{ $item->description }}
                    </div>
                    <div class="text-end mt-2">
                        <button class="btn btn-link p-0 toggle-description"
                                style="color: var(--primary-color); font-size: 0.9em; text-decoration: none;">
                            Voir plus
                        </button>
                    </div>
                @endif
            </div>

            <!-- Catégorie et localisation -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <span class="badge" style="background-color: var(--primary-color);">
                        {{ $item->category->name }}
                    </span>
                    @if($item->location)
                        <span class="ms-2" style="color: var(--text-color);">
                            <i class="bi bi-geo-alt-fill me-1"></i>{{ $item->location }}
                        </span>
                    @endif
                </div>
                <div style="color: var(--text-color);">
                    <i class="bi bi-person-fill me-1"></i>Vendeur : {{ $item->user->name }}
                </div>
            </div>

            <!-- Images -->
            @if($item->images->isNotEmpty())
                <div class="mb-4">
                    <h5 style="color: var(--dark-color); margin-bottom: 1rem;">Images</h5>
                    <div class="row">
                        @foreach($item->images as $image)
                            <div class="col-md-3 mb-3">
                                <img src="{{ $image->url }}" class="img-fluid rounded" alt="{{ $image->original_name ?? 'Image de l\'article' }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Statut -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <span class="badge" style="background-color: {{ $item->is_active ? 'var(--primary-color)' : 'var(--tertiary-color)' }};">
                    {{ $item->is_active ? 'Actif' : 'Inactif' }}
                </span>
                <span class="badge" style="background-color: {{ $item->is_sold ? '#dc3545' : 'var(--tertiary-color)' }};">
                    {{ $item->is_sold ? 'Vendu' : 'Disponible' }}
                </span>
            </div>

            <!-- Section des avis -->
            @php
                $averageRating = $item->reviews->avg('rating') ?? 0;
                $reviewCount = $item->reviews->count();
                $userReview = auth()->check() ? $item->reviews()->where('user_id', auth()->id())->first() : null;
            @endphp

            <div class="mb-4">
                <h5 style="color: var(--dark-color); margin-bottom: 1rem;">Avis clients</h5>
                <div class="d-flex align-items-center mb-3">
                    <div class="text-warning me-2">
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
                    <span style="color: var(--text-color);">{{ number_format($averageRating, 1) }} ({{ $reviewCount }} avis)</span>
                </div>

                <!-- Formulaire pour laisser un avis -->
                @auth
                    @if(!$userReview)
                        <div class="service-card p-3 mb-3">
                            <h6 style="color: var(--dark-color); margin-bottom: 1rem;">Laisser un avis</h6>
                            <form action="{{ route('marketplace.reviews.store', $item) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label" style="color: var(--dark-color);">Note</label>
                                    <div class="star-rating">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" id="star-new-{{ $i }}" name="rating" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }} />
                                            <label for="star-new-{{ $i }}" title="{{ $i }} étoiles">
                                                <i class="bi bi-star"></i>
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="comment-new" class="form-label" style="color: var(--dark-color);">Commentaire (optionnel)</label>
                                    <textarea class="form-control" id="comment-new" name="comment" rows="3">{{ old('comment') }}</textarea>
                                </div>
                                <button type="submit" class="btn-retro">
                                    <i class="bi bi-send me-2"></i> Soumettre l'avis
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-check-circle-fill me-2"></i> Vous avez déjà laissé un avis pour cet article.
                        </div>
                    @endif
                @endauth

                <!-- Liste des avis existants -->
                @if($reviewCount > 0)
                    <div class="reviews-list">
                        @foreach($item->reviews as $review)
                            <div class="service-card p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <span style="font-weight: bold; color: var(--dark-color);">{{ $review->user->name }}</span>
                                        <div class="text-warning">
                                            @for($i = 0; $i < 5; $i++)
                                                @if($i < $review->rating)
                                                    <i class="bi bi-star-fill"></i>
                                                @else
                                                    <i class="bi bi-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                    <small style="color: var(--text-color);">{{ $review->created_at->format('d/m/Y') }}</small>
                                </div>
                                @if($review->comment)
                                    <p style="color: var(--text-color); margin-bottom: 1rem;">{{ $review->comment }}</p>
                                @endif
                                @auth
                                    @if(auth()->id() !== $review->user_id)
                                        <div class="d-flex gap-2">
                                            <!-- Bouton "Utile" -->
                                            <form action="{{ route('marketplace.reviews.helpful', $review) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm" style="background-color: var(--primary-color); color: white; border-radius: 8px;">
                                                    <i class="bi bi-hand-thumbs-up me-1"></i> Utile ({{ $review->is_helpful }})
                                                </button>
                                            </form>

                                            <!-- Bouton "Signaler" -->
                                            <button type="button" class="btn btn-sm" style="background-color: var(--tertiary-color); color: white; border-radius: 8px;"
                                                    onclick="openReportModal('{{ $review->id }}')">
                                                <i class="bi bi-flag me-1"></i> Signaler
                                            </button>
                                        </div>
                                    @endif
                                @endauth
                            </div>

                            <!-- Modal pour signaler un avis (version JavaScript pur) -->
                            <div id="reportModal-{{ $review->id }}" class="custom-modal">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5>Signaler un avis</h5>
                                        <button type="button" class="btn-close" onclick="closeReportModal('{{ $review->id }}')">×</button>
                                    </div>
                                    <form method="POST" action="{{ route('marketplace.reviews.report', $review) }}">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Raison du signalement</label>
                                                <select class="form-select" name="reason" required>
                                                    <option value="" selected disabled>Sélectionnez une raison</option>
                                                    <option value="spam">Contenu indésirable ou spam</option>
                                                    <option value="abusive">Langage abusif ou haineux</option>
                                                    <option value="fake">Avis faux ou trompeur</option>
                                                    <option value="other">Autre</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Commentaire (optionnel)</label>
                                                <textarea class="form-control" name="comment" rows="3"></textarea>
                                            </div>
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" name="notify_admin" value="1" checked>
                                                <label class="form-check-label">Notifier l'administrateur</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" onclick="closeReportModal('{{ $review->id }}')">Annuler</button>
                                            <button type="submit" class="btn btn-primary">Envoyer le signalement</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="bi bi-chat-left-text" style="font-size: 2rem; color: var(--secondary-color); margin-bottom: 1rem;"></i>
                        <p style="color: var(--text-color);">Aucun avis pour cet article.</p>
                    </div>
                @endif
            </div>

            <!-- Boutons d'action pour le propriétaire -->
            @auth
                @if(auth()->id() === $item->user_id)
                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('marketplace.items.edit', $item) }}" class="btn-retro me-2">
                            <i class="bi bi-pencil-square me-2"></i>Modifier
                        </a>
                        <form action="{{ route('marketplace.items.destroy', $item) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr ?')">
                                <i class="bi bi-trash-fill me-2"></i>Supprimer
                            </button>
                        </form>
                    </div>
                @endif
            @endauth
        </div>
    </div>

    <!-- CSS pour les étoiles de notation et les modales -->
    <style>
        /* Système de notation par étoiles */
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 0.3rem;
            margin-top: 0.5rem;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            font-size: 1.5rem;
            color: #ccc;
            cursor: pointer;
            transition: color 0.2s;
        }

        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #ffc107;
        }

        /* Style pour les étoiles dans les avis existants */
        .text-warning .bi-star-fill,
        .text-warning .bi-star-half,
        .text-warning .bi-star {
            font-size: 1rem;
        }

        /* Style pour les badges */
        .badge {
            padding: 0.35em 0.65em;
            border-radius: 0.25rem;
        }

        /* Style pour les modales personnalisées */
        .custom-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow: auto;
        }

        .custom-modal .modal-content {
            background-color: white;
            margin: auto; /* Centrage horizontal et vertical */
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: absolute;
            top: 50%; /* Positionne le haut de la modale au milieu de l'écran */
            left: 50%; /* Positionne la gauche de la modale au milieu de l'écran */
            transform: translate(-50%, -50%); /* Décale de 50% de sa propre largeur/hauteur pour un centrage parfait */
        }

        .custom-modal .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .custom-modal .modal-body {
            margin-bottom: 15px;
        }

        .custom-modal .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0;
            line-height: 1;
        }
    </style>

    <!-- JavaScript pour les modales et le bouton Voir plus/Voir moins -->
    <script>
        // Fonctions pour gérer les modales
        function openReportModal(reviewId) {
            document.getElementById('reportModal-' + reviewId).style.display = 'block';
            document.body.style.overflow = 'hidden'; // Désactive le scroll en arrière-plan
        }

        function closeReportModal(reviewId) {
            document.getElementById('reportModal-' + reviewId).style.display = 'none';
            document.body.style.overflow = 'auto'; // Réactive le scroll
        }

        // Ferme la modale si on clique en dehors
        window.onclick = function(event) {
            if (event.target.classList.contains('custom-modal')) {
                event.target.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }

        // Gestion des descriptions tronquées
        document.addEventListener('DOMContentLoaded', function() {
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
