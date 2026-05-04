<!--
====================================================================================================
FICHIER BLADE : LISTE DES PRESTATAIRES AVEC PAGINATION (providers/liste_paginee.blade.php)
====================================================================================================
Description : Affiche une liste paginée de prestataires avec des cartes cliquables et un sélecteur de page.
              Intègre le design glassmorphisme et une structure responsive.
-->

@if(isset($providers) && $providers->isNotEmpty())
    <!--
    ==================================================================================================
    CONTAINER PRINCIPAL (service-card)
    ==================================================================================================
    Carte principale avec effet glassmorphisme pour contenir la liste des prestataires.
    -->
    <div class="service-card">
        <!--
        --------------------------------------------------------------------------------------------------
        EN-TÊTE DE LA SECTION
        --------------------------------------------------------------------------------------------------
        Conteneur flexible pour le titre et les éventuels boutons d'action (filtres, tri, etc.).
        mb-3 : marge basse pour espacer le contenu suivant.
        -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>{{ __('messages.liste_des_prestataires') }}</h3>
            <!-- Espace réservé pour des boutons d'action futurs (ex: filtre, tri) -->
        </div>

        <!--
        --------------------------------------------------------------------------------------------------
        GRILLE RESPONSIVE DES PRESTATAIRES (Bootstrap Row)
        --------------------------------------------------------------------------------------------------
        Utilise le système de grille Bootstrap (12 colonnes) pour afficher 3 prestataires par ligne sur écran moyen/large.
        -->
        <div class="row">
            @foreach($providers as $provider)
                <!--
                --------------------------------------------------------------------------------------
                COLONNE INDIVIDUELLE (col-md-4)
                --------------------------------------------------------------------------------------
                Occupe 4 colonnes sur 12 en écran moyen/large (soit 33% de la largeur).
                mb-3 : marge basse pour espacer les lignes de cartes.
                -->
                <div class="col-md-4 mb-3">
                    <!--
                    ----------------------------------------------------------------------------------
                    LIEN CLIQUABLE VERS LA PAGE DU PRESTATAIRE
                    ----------------------------------------------------------------------------------
                    text-decoration-none : supprime le soulignement par défaut des liens.
                    -->
                    <a href="{{ route('providers.show', $provider) }}" class="text-decoration-none" aria-label="Voir les détails de {{ $provider->first_name }} {{ $provider->last_name }}">
                        <!--
                        ------------------------------------------------------------------------------
                        CARTE CLIQUABLE (clickable-card)
                        ------------------------------------------------------------------------------
                        h-100 : force une hauteur uniforme pour toutes les cartes de la ligne.
                        clickable-card : applique les styles glassmorphisme et les effets de survol.
                        -->
                        <div class="card h-100 clickable-card">
                            <!--
                            --------------------------------------------------------------------------
                            CORPS DE LA CARTE (card-body)
                            --------------------------------------------------------------------------
                            Contient les informations du prestataire avec un padding interne.
                            -->

                            <!-- Section pour le logo du prestataire -->
                            @if($provider->image)
                                @php
                                    $logoPath = $provider->image->path;
                                    $logoBaseName = pathinfo($logoPath, PATHINFO_FILENAME);
                                    $logoBaseName = preg_replace('/-original$/', '', $logoBaseName);
                                    $sizes = [
                                        '380' => '(max-width: 576px) 380px',
                                        '540' => '(max-width: 768px) 540px',
                                        '700' => '700px'
                                    ];
                                @endphp

                                <div class="text-center pt-3">
                                    <picture>
                                        <!-- AVIF -->
                                        <source type="image/avif" srcset="
                {{ asset("storage/logos/{$logoBaseName}-380w.avif") }} 380w,
                {{ asset("storage/logos/{$logoBaseName}-540w.avif") }} 540w,
                {{ asset("storage/logos/{$logoBaseName}-700w.avif") }} 700w"
                                                sizes="{{ implode(', ', $sizes) }}">

                                        <!-- WebP -->
                                        <source type="image/webp" srcset="
                {{ asset("storage/logos/{$logoBaseName}-380w.webp") }} 380w,
                {{ asset("storage/logos/{$logoBaseName}-540w.webp") }} 540w,
                {{ asset("storage/logos/{$logoBaseName}-700w.webp") }} 700w"
                                                sizes="{{ implode(', ', $sizes) }}">

                                        <!-- PNG -->
                                        <source type="image/png" srcset="
                {{ asset("storage/logos/{$logoBaseName}-380w.png") }} 380w,
                {{ asset("storage/logos/{$logoBaseName}-540w.png") }} 540w,
                {{ asset("storage/logos/{$logoBaseName}-700w.png") }} 700w"
                                                sizes="{{ implode(', ', $sizes) }}">

                                        <!-- Fallback JPEG -->
                                        <img
                                            src="{{ asset("storage/{$logoPath}") }}"
                                            srcset="
                    {{ asset("storage/logos/{$logoBaseName}-380w.jpg") }} 380w,
                    {{ asset("storage/logos/{$logoBaseName}-540w.jpg") }} 540w,
                    {{ asset("storage/logos/{$logoBaseName}-700w.jpg") }} 700w"
                                            sizes="{{ implode(', ', $sizes) }}"
                                            alt="Logo de {{ $provider->last_name }} {{ $provider->first_name }}"
                                            class="img-fluid rounded mx-auto d-block"
                                            style="max-height: 80px; max-width: 100%; object-fit: contain;"
                                            loading="lazy"
                                            decoding="async"
                                            onerror="this.src='/images/placeholder.jpg'; this.onerror=null;"
                                        >
                                    </picture>
                                </div>
                            @endif


                            <div class="card-body">
                                <!--
                                ----------------------------------------------------------------------
                                NOM DU PRESTATAIRE (card-title)
                                ----------------------------------------------------------------------
                                Affiché en taille h5 avec une couleur primaire et une marge basse réduite.
                                -->
                                <h5 class="card-title">{{ $provider->last_name }} {{ $provider->first_name }}</h5>

                                <!--
                                ----------------------------------------------------------------------
                                INFORMATIONS DU PRESTATAIRE (card-text)
                                ----------------------------------------------------------------------
                                Paragraphe contenant les détails conditionnels (adresse, téléphone, site web, services).
                                -->
                                <p class="card-text">
                                    <!--
                                    --------------------------------------------------------------
                                    ADRESSE (si disponible)
                                    --------------------------------------------------------------
                                    Icône de localisation + adresse.
                                    -->
                                    @if($provider->address)
                                        <i class="bi bi-geo-alt"></i> {{ $provider->address }}<br>
                                    @endif

                                    <!--
                                    --------------------------------------------------------------
                                    TÉLÉPHONE MOBILE (si disponible)
                                    --------------------------------------------------------------
                                    Icône de téléphone + numéro.
                                    -->
                                    @if($provider->mobile_phone)
                                        <i class="bi bi-telephone"></i> {{ $provider->mobile_phone }}<br>
                                    @endif

                                    <!--
                                    --------------------------------------------------------------
                                    SITE WEB (si disponible)
                                    --------------------------------------------------------------
                                    Icône de globe + URL.
                                    -->
                                    @if($provider->website)
                                        <i class="bi bi-globe"></i> <span>{{ $provider->website }}</span><br>
                                    @endif

                                    <!--
                                    --------------------------------------------------------------
                                    SERVICES PROPOSÉS
                                    --------------------------------------------------------------
                                    Titre en gras suivi des badges de catégories de services.
                                    -->
                                    <strong>Services:</strong>
                                    @foreach($provider->serviceCategories as $category)
                                        <!--
                                        ----------------------------------------------------------
                                        BADGE DE CATÉGORIE DE SERVICE
                                        ----------------------------------------------------------
                                        Lien vers la page de la catégorie avec un badge Bootstrap.
                                        bg-secondary : couleur de fond grise pour le badge.
                                        text-decoration-none : supprime le soulignement du lien.
                                        -->
                                        <a href="{{ route('service_categories.show', $category->id) }}" class="badge bg-secondary text-decoration-none">
                                            {{ $category->name }}
                                        </a>
                                    @endforeach
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <!--
        ==================================================================================================
        SECTION DE PAGINATION
        ==================================================================================================
        Conteneur flexible pour la pagination standard et le sélecteur de page personnalisé.
        mt-4 : marge haute pour séparer du contenu précédent.
        -->
        <div class="d-flex flex-column align-items-center mt-4">
            <!--
            --------------------------------------------------------------------------------------------------
            PAGINATION STANDARD (Bootstrap)
            --------------------------------------------------------------------------------------------------
            appends() : conserve tous les paramètres de requête sauf 'page'.
            links() : génère les liens de pagination à partir de l'instance $providers.
            -->
            <div class="mb-3">
                {{ $providers->appends(request()->except('page'))->links('vendor.pagination.bootstrap-5') }}
            </div>

            <!--
            --------------------------------------------------------------------------------------------------
            SÉLECTEUR DE PAGE PERSONNALISÉ
            --------------------------------------------------------------------------------------------------
            Formulaire pour sauter directement à une page spécifique.
            -->
            <div class="page-jump d-flex align-items-center">
                <!--
                ------------------------------------------------------------------------------
                FORMULAIRE DE SAUT DE PAGE
                ------------------------------------------------------------------------------
                d-flex : affiche les éléments en ligne.
                align-items-center : centre verticalement les éléments.
                -->
                <form method="GET" action="{{ url()->current() }}" class="d-flex align-items-center">
                    <!--
                    --------------------------------------------------------------
                    LABEL DU CHAMP
                    --------------------------------------------------------------
                    me-2 : marge droite pour espacer le champ.
                    text-muted : texte atténué.
                    small : taille de texte réduite.
                    -->
                    <span class="me-2 text-muted small">Aller à la page</span>

                    <!--
                    --------------------------------------------------------------
                    CHAMP DE SAISIE DU NUMÉRO DE PAGE
                    --------------------------------------------------------------
                    type="number" : clavier numérique sur mobile.
                    min/max : limite les valeurs aux pages disponibles.
                    value : page actuelle par défaut.
                    style="width: 60px" : largeur fixe pour le champ.
                    -->
                    <input type="number"
                           name="page"
                           value="{{ request('page', 1) }}"
                           min="1"
                           max="{{ $providers->lastPage() }}"
                           class="form-control form-control-sm me-2"
                           style="width: 60px;"
                           title="Numéro de page">

                    <!--
                    --------------------------------------------------------------
                    BOUTON DE SOUMISSION
                    --------------------------------------------------------------
                    btn-sm : bouton de petite taille.
                    btn-outline-primary : style contour avec couleur primaire.
                    -->
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>

                <!--
                --------------------------------------------------------------
                INDICATEUR DE PAGE TOTALE
                --------------------------------------------------------------
                ms-2 : marge gauche pour espacer du bouton.
                -->
                <span class="ms-2 text-muted small">/ {{ $providers->lastPage() }}</span>
            </div>
        </div>
    </div>
@else
    <!--
    ==================================================================================================
    ÉTAT VIDE (AUCUN PRESTATAIRE)
    ==================================================================================================
    Affiche une alerte informative si aucun prestataire n'est trouvé.
    role="alert" : améliore l'accessibilité pour les lecteurs d'écran.
    -->
    <div class="alert alert-info" role="alert">
        Aucun prestataire trouvé.
    </div>
@endif

<!--
====================================================================================================
STYLES CSS INTÉGRÉS
====================================================================================================
Définit les styles pour les cartes cliquables et le sélecteur de page.
-->
<style>
    /* -------------------------------------------------------------------------------------------------
    STYLE DES CARTES CLIQUABLES (EFFET GLASSMORPHISME)
    ------------------------------------------------------------------------------------------------- */
    .clickable-card {
        /* Transition fluide pour les effets de survol */
        transition: all 0.3s ease;

        /* Bordure subtile avec transparence */
        border: 1px solid rgba(255, 255, 255, 0.3);

        /* Coins arrondis */
        border-radius: 16px;

        /* Fond semi-transparent avec effet de flou (glassmorphisme) */
        background: rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(12px);

        /* Ombre légère pour le relief */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* -------------------------------------------------------------------------------------------------
    EFFETS AU SURVOL DES CARTES
    ------------------------------------------------------------------------------------------------- */
    .clickable-card:hover {
        /* Déplacement vers le haut pour un effet de "lévitation" */
        transform: translateY(-5px);

        /* Ombre plus marquée */
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);

        /* Fond moins transparent */
        background: rgba(255, 255, 255, 0.4);

        /* Curseur "main" pour indiquer le clic possible */
        cursor: pointer;
    }

    /* -------------------------------------------------------------------------------------------------
    STYLE DU SÉLECTEUR DE PAGE
    ------------------------------------------------------------------------------------------------- */
    .page-jump {
        /* Taille de texte réduite */
        font-size: 0.9rem;
    }

    /* Style du champ de saisie */
    .page-jump .form-control-sm {
        /* Padding réduit pour un champ compact */
        padding: 0.25rem 0.5rem;

        /* Hauteur fixe */
        height: 30px;

        /* Texte centré */
        text-align: center;
    }

    /* Style au focus du champ */
    .page-jump .form-control-sm:focus {
        /* Ombre de focus subtile */
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);

        /* Bordure bleue au focus */
        border-color: #0d6efd;
    }

    /* -------------------------------------------------------------------------------------------------
    STYLE DES LIENS (SANS SOULIGNEMENT)
    ------------------------------------------------------------------------------------------------- */
    .text-decoration-none {
        /* Supprime le soulignement */
        text-decoration: none !important;

        /* Hérite de la couleur du texte parent */
        color: inherit;
    }

    /* Style du corps de la carte */
    .clickable-card .card-body {
        /* Couleur de texte foncée pour un bon contraste */
        color: #212529;

        /* Padding interne généreux */
        padding: 1rem;
    }

    /* Style du titre de la carte */
    .clickable-card .card-title {
        /* Couleur bleue primaire */
        color: #0d6efd;

        /* Marge basse réduite */
        margin-bottom: 0.75rem;
    }

    /* Style de l'icône de la flèche */
    .bi-arrow-right {
        /* Couleur bleue pour correspondre aux liens */
        color: #0d6efd;

        /* Taille légèrement agrandie */
        font-size: 1.1rem;

        /* Marge gauche pour espacer de l'élément précédent */
        margin-left: 0.5rem;
    }

    /* -------------------------------------------------------------------------------------------------
    FALLBACK POUR LES NAVIGATEURS NE SUPPORTANT PAS BACKDROP-FILTER
    ------------------------------------------------------------------------------------------------- */
    @supports not (backdrop-filter: blur(10px)) {
        .clickable-card {
            /* Fond semi-transparent solide comme solution de repli */
            background: rgba(255, 255, 255, 0.9) !important;
        }
    }

    .bg-secondary {
        background-color: #48D1CC !important;
        color: white;
    }

</style>
