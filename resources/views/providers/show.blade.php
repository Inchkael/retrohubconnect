@extends('layouts.layout')

@section('title', 'Profil de ' . $user->getFullName())

@section('content')
    <div class="container">
        <!-- En-tête du profil -->
        <div class="service-card mb-4 text-center">
            @if($user->image)
                @php
                    $logoPath = $user->image->path;
                    $logoBaseName = pathinfo($logoPath, PATHINFO_FILENAME);
                    $logoBaseName = preg_replace('/-original$/', '', $logoBaseName);
                    $sizes = [
                        '380' => '(max-width: 576px) 380px',
                        '540' => '(max-width: 768px) 540px',
                        '700' => '700px'
                    ];
                @endphp
                <div class="mb-3">
                    <picture>
                        <source type="image/avif" srcset="{{ asset("storage/logos/{$logoBaseName}-380w.avif") }} 380w, {{ asset("storage/logos/{$logoBaseName}-540w.avif") }} 540w, {{ asset("storage/logos/{$logoBaseName}-700w.avif") }} 700w" sizes="{{ implode(', ', $sizes) }}">
                        <source type="image/webp" srcset="{{ asset("storage/logos/{$logoBaseName}-380w.webp") }} 380w, {{ asset("storage/logos/{$logoBaseName}-540w.webp") }} 540w, {{ asset("storage/logos/{$logoBaseName}-700w.webp") }} 700w" sizes="{{ implode(', ', $sizes) }}">
                        <source type="image/png" srcset="{{ asset("storage/logos/{$logoBaseName}-380w.png") }} 380w, {{ asset("storage/logos/{$logoBaseName}-540w.png") }} 540w, {{ asset("storage/logos/{$logoBaseName}-700w.png") }} 700w" sizes="{{ implode(', ', $sizes) }}">
                        <img src="{{ asset("storage/{$logoPath}") }}" srcset="{{ asset("storage/logos/{$logoBaseName}-380w.jpg") }} 380w, {{ asset("storage/logos/{$logoBaseName}-540w.jpg") }} 540w, {{ asset("storage/logos/{$logoBaseName}-700w.jpg") }} 700w" sizes="{{ implode(', ', $sizes) }}" alt="Logo de {{ $user->getFullName() }}" class="img-fluid rounded mx-auto d-block" style="max-height: 120px; max-width: 100%; object-fit: contain;" loading="lazy" decoding="async" onerror="this.src='/images/placeholder.jpg'; this.onerror=null;">
                    </picture>
                </div>
            @else
                <div class="mb-3">
                    <div class="p-4 bg-light rounded mx-auto" style="max-width: 200px; max-height: 120px;">
                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                    </div>
                </div>
            @endif

            <h1>Profil de {{ $user->getFullName() }}</h1>
            <p class="lead">Prestataire de services Bien-Être</p>
        </div>

        <!-- Informations principales -->
        <div class="row mb-4">
            <!-- Colonne gauche : Coordonnées -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Coordonnées</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="bi bi-envelope"></i> <strong>Email:</strong> {{ $user->email }}
                                <a href="#"
                                   class="btn btn-primary"
                                   data-bs-toggle="modal"
                                   data-bs-target="#contactModal">
                                    <i class="bi bi-envelope me-2"></i> Contacter {{ $user->last_name }} {{ $user->first_name }}
                                </a>
                            </li>
                            @if($user->mobile_phone)
                                <li class="list-group-item">
                                    <i class="bi bi-telephone"></i> <strong>Téléphone:</strong> {{ $user->mobile_phone }}
                                </li>
                            @endif
                            @if($user->address)
                                <li class="list-group-item">
                                    <i class="bi bi-geo-alt"></i> <strong>Adresse:</strong> {{ $user->address }}
                                </li>
                            @endif
                            @if($user->website)
                                <li class="list-group-item">
                                    <i class="bi bi-globe"></i> <strong>Site web:</strong>
                                    <a href="{{ $user->website }}" target="_blank">{{ $user->website }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
                <!-- Section des commentaires -->
                <div class="service-card mb-4">
                    <h3>Laisser un commentaire</h3>

                    @auth
                        <form method="POST" action="{{ route('commentaires.store') }}">
                            @csrf
                            <input type="hidden" name="PrestataireID" value="{{ $user->id }}">

                            <div class="mb-3">
                                <label for="Titre" class="form-label">Titre :</label>
                                <input type="text" class="form-control" id="Titre" name="Titre" required>
                            </div>

                            <div class="mb-3">
                                <label for="Contenu" class="form-label">Contenu :</label>
                                <textarea class="form-control" id="Contenu" name="Contenu" rows="3" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="Cote" class="form-label">Note (0-5) :</label>
                                <select class="form-select" id="Cote" name="Cote" required>
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3" selected>3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Soumettre</button>
                        </form>
                    @else
                        <div class="alert alert-info">
                            Vous devez être connecté pour laisser un commentaire. <a href="{{ route('login') }}">Se connecter</a>
                        </div>
                    @endauth
                </div>

                <!-- Affichage des commentaires existants -->
                @if(isset($user->commentairesPrestataire) && $user->commentairesPrestataire->isNotEmpty())
                    <div class="service-card mb-4">
                        <h3>Commentaires</h3>
                        @foreach($user->commentairesPrestataire as $commentaire)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $commentaire->Titre }}</h5>
                                    <p class="card-text">{{ $commentaire->Contenu }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">Note : {{ $commentaire->Cote }}/5</small><br>
                                            <div class="d-flex align-items-center mb-1">
                                                <div class="star-rating">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= $commentaire->Cote)
                                                            <i class="bi bi-star-fill text-warning"></i>
                                                        @else
                                                            <i class="bi bi-star text-warning"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <small class="text-muted ms-2">Posté par : {{ $commentaire->internaute->getFullName() }}</small>
                                            </div>

                                            <small class="text-muted">Posté par : {{ $commentaire->internaute->getFullName() }}</small>
                                        </div>
                                        <small class="text-muted">
                                            @if($commentaire->Encodage instanceof \DateTime)
                                                {{ $commentaire->Encodage->format('d/m/Y H:i') }}
                                            @else
                                                {{ $commentaire->Encodage }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($user->address)
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-map me-2"></i>Localisation</span>
                                @auth
                                    @if(auth()->user()->isAdmin() && (!$user->latitude || !$user->longitude))
                                        <button class="btn btn-sm btn-outline-secondary geocode-btn"
                                                data-user-id="{{ $user->id }}"
                                                data-user-address="{{ addslashes($user->address) }}">
                                            <i class="bi bi-geo-alt me-1"></i> Géocoder
                                        </button>
                                    @endif
                                    @if($user->latitude && $user->longitude)
                                        <button id="get-route" class="btn btn-sm btn-outline-primary ms-2">
                                            <i class="bi bi-signpost-2 me-1"></i> Itinéraire
                                        </button>
                                    @endif
                                @endauth
                            </h5>

                            <!-- Section d'information sur les coordonnées -->
                            <div class="alert alert-light mb-3 p-2">
                                @if($user->latitude && $user->longitude)
                                    <div class="d-flex align-items-center text-success">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        <div>
                                            <strong>Localisation précise disponible</strong><br>
                                            <small>
                                                Latitude: {{ number_format($user->latitude, 6) }} |
                                                Longitude: {{ number_format($user->longitude, 6) }}
                                            </small>
                                        </div>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center text-warning">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        <div>
                                            <strong>Localisation non géocodée</strong><br>
                                            <small>Adresse enregistrée: {{ $user->address }}</small>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Conteneur principal de la carte -->
                            <div class="position-relative" style="height: 300px; width: 100%; border-radius: 8px; overflow: hidden;">

                                <!-- Carte Leaflet (toujours présente mais adaptée) -->
                                <div id="provider-map"
                                     style="height: 100%; width: 100%;"
                                     data-has-coords="{{ $user->latitude && $user->longitude ? 'true' : 'false' }}"
                                     data-default-lat="50.640281"
                                     data-default-lon="4.666745"
                                     data-user-lat="{{ $user->latitude ?? '' }}"
                                     data-user-lon="{{ $user->longitude ?? '' }}"
                                     data-user-address="{{ addslashes($user->address) }}"></div>

                                <!-- Overlay pour les coordonnées manquantes -->
                                @unless($user->latitude && $user->longitude)
                                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3"
                                         style="background-color: rgba(255, 255, 255, 0.9); z-index: 1000;">
                                        <div class="text-center">
                                            <i class="bi bi-geo-alt" style="font-size: 2.5rem; color: #6c757d; margin-bottom: 1rem;"></i>
                                            <h5 class="mb-2">Localisation non disponible</h5>
                                            <p class="text-muted mb-3" style="max-width: 80%; margin: 0 auto;">
                                                @if(auth()->check() && auth()->user()->isAdmin())
                                                    Cette adresse n'a pas encore été géocodée avec précision.
                                                    <button class="btn btn-sm btn-outline-primary mt-2 geocode-btn"
                                                            data-user-id="{{ $user->id }}"
                                                            data-user-address="{{ addslashes($user->address) }}">
                                                        <i class="bi bi-geo-alt me-1"></i> Géocoder cette adresse
                                                    </button>
                                                @else
                                                    La localisation précise de ce prestataire n'est pas encore disponible.
                                                    La carte montre une vue par défaut de la Belgique.
                                                @endif
                                            </p>
                                            <small class="text-muted">
                                                <i class="bi bi-info-circle me-1"></i>
                                                Coordonnées par défaut: 50.640281, 4.666745 (centre de la Belgique)
                                            </small>
                                        </div>
                                    </div>
                                @endunless
                            </div>

                            @auth
                                @if($user->latitude && $user->longitude)
                                    <div id="route-instructions" class="mt-3" style="display: none;">
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <span id="route-status">Calcul de l'itinéraire en cours...</span>
                                        </div>
                                        <div id="route-steps" class="mt-2"></div>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-light mt-2">
                                    <i class="bi bi-lock me-2"></i>
                                    Connectez-vous pour calculer un itinéraire vers ce prestataire.
                                </div>
                            @endauth
                        </div>
                    </div>
                @endif

                <style>
                    /* Style pour la carte et les overlays */
                    #provider-map {
                        height: 100%;
                        width: 100%;
                        border-radius: 8px;
                        transition: all 0.3s ease;
                    }

                    /* Style pour les popups Leaflet */
                    .leaflet-popup-content-wrapper {
                        border-radius: 8px;
                        padding: 0;
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                    }

                    .leaflet-popup-content {
                        margin: 0;
                        padding: 12px;
                        min-width: 200px;
                        max-width: 300px;
                    }

                    .leaflet-popup-tip {
                        background-color: white;
                    }

                    /* Style pour le contenu des popups */
                    .custom-popup-content h6 {
                        margin: 0 0 8px 0;
                        color: #2E8B8B;
                        font-size: 16px;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }

                    .custom-popup-content p {
                        margin: 0 0 8px 0;
                        color: #333;
                        word-break: break-word;
                        max-height: 80px;
                        overflow-y: auto;
                    }

                    /* Style pour les messages d'erreur */
                    .location-warning {
                        background-color: rgba(255, 255, 255, 0.9);
                        border-radius: 8px;
                        padding: 1rem;
                        margin-bottom: 1rem;
                    }
                     .star-rating {
                         color: #ffc107;
                         font-size: 0.9rem;
                         display: inline-flex;
                     }

                    .star-rating i {
                        margin-right: 2px;
                    }

                </style>





            </div>

            <!-- Colonne droite : Services -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Services proposés</h5>
                        @if($user->serviceCategories->isNotEmpty())
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($user->serviceCategories as $category)
                                    <a href="{{ route('service_categories.show', $category->id) }}"
                                       class="badge bg-service text-decoration-none"
                                       style="font-size: 0.9rem; padding: 0.5em 0.75em;">
                                        <i class="bi bi-{{ $category->icon ?? 'tag' }} me-1"></i>
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">Ce prestataire ne propose aucun service pour le moment.</p>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($user->promotions->isNotEmpty())
                            <div class="service-card mb-4">
                                <h3>Promotions</h3>
                                @auth
                                    @if(auth()->id() === $user->id)
                                        <div class="mb-4">
                                            <a href="{{ route('promotions.create') }}" class="btn btn-primary">
                                                <i class="bi bi-plus-circle me-2"></i> Ajouter une promotion
                                            </a>
                                        </div>
                                    @endif
                                @endauth

                            @foreach($user->promotions as $promotion)
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $promotion->Nom }}</h5>
                                            <p class="card-text">{{ $promotion->Description }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Catégorie: {{ $promotion->categorieDeServices->name }}</small><br>
                                                    <small class="text-muted">Du {{ $promotion->Début->format('d/m/Y') }} au {{ $promotion->Fin->format('d/m/Y') }}</small>
                                                </div>
                                                <div>
                                                    @if($promotion->DocumentPdf)
                                                        <a href="{{ Storage::url($promotion->DocumentPdf) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                            <i class="bi bi-file-earmark-pdf me-1"></i> Voir le PDF
                                                        </a>
                                                    @endif
                                                    @auth
                                                        @if(auth()->id() === $user->id)
                                                            <form action="{{ route('promotions.destroy', $promotion) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette promotion ?')">
                                                                    <i class="bi bi-trash me-1"></i> Supprimer
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endauth
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>


                </div>
            </div>
        </div>

        <!-- Description -->
        @if($user->description)
            <div class="service-card mb-4">
                <h3>À propos de {{ $user->first_name }}</h3>
                <p>{{ $user->description }}</p>
            </div>
        @endif
    </div>
@endsection

<!-- Modal Contact -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contactModalLabel">Contacter {{ $user->last_name }} {{ $user->first_name }}</h5>
            </div>
            <form method="POST" action="{{ route('providers.contact', $user->id) }}">
                @csrf
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" class="form-control" name="nom" value="{{ auth()->check() ? auth()->user()->first_name . ' ' . auth()->user()->last_name : '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control" name="email" value="{{ auth()->check() ? auth()->user()->email : '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Objet *</label>
                        <input type="text" class="form-control" name="objet" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message *</label>
                        <textarea class="form-control" name="message" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- Mise à jour de l'attribut pour Bootstrap 5 -->
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
    // Attendre que tout soit chargé
    document.addEventListener('DOMContentLoaded', function() {
        // Vérifier que Leaflet est chargé
        if (typeof L === 'undefined') {
            console.error('Leaflet non chargé');
            document.getElementById('provider-map').innerHTML = `
            <div class="alert alert-danger m-3">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Erreur: Impossible de charger la carte. Veuillez rafraîchir la page.
            </div>
        `;
            return;
        }

        // Récupérer les données depuis les attributs data
        const mapElement = document.getElementById('provider-map');
        if (!mapElement) return;

        const hasCoords = mapElement.getAttribute('data-has-coords') === 'true';
        const defaultLat = parseFloat(mapElement.getAttribute('data-default-lat'));
        const defaultLon = parseFloat(mapElement.getAttribute('data-default-lon'));
        const userLat = parseFloat(mapElement.getAttribute('data-user-lat')) || defaultLat;
        const userLon = parseFloat(mapElement.getAttribute('data-user-lon')) || defaultLon;
        const userAddress = mapElement.getAttribute('data-user-address') || '';

        // Coordonnées à utiliser
        const mapCenter = hasCoords ? [userLat, userLon] : [defaultLat, defaultLon];
        const zoomLevel = hasCoords ? 15 : 8; // Zoom plus large pour la vue par défaut

        // Initialiser la carte
        const map = L.map('provider-map', {
            center: mapCenter,
            zoom: zoomLevel,
            zoomControl: true,
            scrollWheelZoom: true,
            doubleClickZoom: true,
            dragging: true,
            tap: true,
            touchZoom: true
        });

        // Ajouter les tuiles OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 18
        }).addTo(map);

        // Ajouter un marqueur seulement si coordonnées valides
        if (hasCoords) {
            const popupContent = `
            <div style="min-width: 200px; max-width: 300px;">
                <h6 style="margin: 0 0 8px; color: #2E8B8B;">{{ addslashes($user->getFullName()) }}</h6>
                <div style="margin: 0 0 8px; max-height: 80px; overflow-y: auto; word-break: break-word;">
                    {{ addslashes($user->address) }}
            </div>
            <small style="color: #666;">
                Lat: ${userLat.toFixed(6)}<br>
                    Lon: ${userLon.toFixed(6)}
                </small>
            </div>
        `;

            L.marker([userLat, userLon])
                .addTo(map)
                .bindPopup(popupContent)
                .openPopup();
        } else {
            // Message pour les coordonnées par défaut
            const defaultPopup = L.popup()
                .setLatLng([defaultLat, defaultLon])
                .setContent(`
                <div style="min-width: 200px; text-align: center;">
                    <i class="bi bi-geo-alt" style="font-size: 1.5rem; color: #6c757d; display: block; margin-bottom: 0.5rem;"></i>
                    <h6 style="margin: 0 0 8px; color: #6c757d;">Localisation par défaut</h6>
                    <p style="margin: 0 0 8px; color: #6c757d; font-size: 0.9rem;">
                        Cette carte montre une vue par défaut de la Belgique.<br>
                        L'adresse exacte n'a pas encore été géocodée.
                    </p>
                    @auth
                @if(auth()->user()->isAdmin())
                <small style="color: #6c757d;">
                    <a href="#" class="geocode-btn text-primary"
                       data-user-id="{{ $user->id }}"
                               data-user-address="{{ addslashes($user->address) }}"
                               style="text-decoration: none;">
                                <i class="bi bi-geo-alt me-1"></i> Géocoder cette adresse
                            </a>
                        </small>
                        @endif
                @endauth
                </div>
            `)
                .openOn(map);
        }

        // Gestion du redimensionnement
        window.addEventListener('resize', function() {
            setTimeout(() => {
                map.invalidateSize();
            }, 200);
        });

        // Gestion du bouton d'itinéraire (uniquement si coordonnées valides)
        @if($user->latitude && $user->longitude)
        const routeButton = document.getElementById('get-route');
        if (routeButton) {
            routeButton.addEventListener('click', function() {
                if (navigator.geolocation) {
                    this.disabled = true;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Localisation...';

                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const userCoords = [position.coords.latitude, position.coords.longitude];
                            L.marker(userCoords).addTo(map).bindPopup("Votre position").openPopup();

                            if (typeof L.Routing !== 'undefined') {
                                L.Routing.control({
                                    waypoints: [
                                        L.latLng(userCoords),
                                        L.latLng([userLat, userLon])
                                    ],
                                    routeWhileDragging: true,
                                    show: false,
                                    addWaypoints: false,
                                    fitSelectedRoutes: true
                                }).addTo(map);

                                document.getElementById('route-instructions').style.display = 'block';
                                document.getElementById('route-status').textContent = 'Itinéraire calculé';
                            }

                            this.disabled = false;
                            this.innerHTML = '<i class="bi bi-signpost-2 me-1"></i> Itinéraire';
                        },
                        (error) => {
                            console.error('Erreur de géolocalisation:', error);
                            this.disabled = false;
                            this.innerHTML = '<i class="bi bi-signpost-2 me-1"></i> Itinéraire';
                            Swal.fire({
                                icon: 'error',
                                title: 'Géolocalisation impossible',
                                text: 'Veuillez autoriser l\'accès à votre position.'
                            });
                        }
                    );
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Non supporté',
                        text: 'Votre navigateur ne supporte pas la géolocalisation.'
                    });
                }
            });
        }
        @endif

        // Gestion du géocodage (pour les admins)
        @auth
        @if(auth()->user()->isAdmin())
        document.querySelectorAll('.geocode-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const userId = this.getAttribute('data-user-id');
                const address = this.getAttribute('data-user-address');

                Swal.fire({
                    title: 'Géocodage en cours...',
                    text: 'Nous essayons de géocoder l\'adresse: ' + address,
                    icon: 'info',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`/admin/users/${userId}/geocode`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ address: address })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Succès',
                                text: 'Adresse géocodée avec succès!',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Échec du géocodage',
                                text: data.message || 'Impossible de géocoder cette adresse.'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Une erreur est survenue lors du géocodage.'
                        });
                    });
            });
        });
        @endif
        @endauth
    });
</script>




