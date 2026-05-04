<!--
====================================================================================================
FICHIER : resources/views/services/show.blade.php
----------------------------------------------------------------------------------------------------
Description : Page de détail d'un service pour la plateforme Bien-Être.
              Affiche la description du service, une galerie d'images, un moteur de recherche par région
              et la liste des prestataires proposant ce service.
----------------------------------------------------------------------------------------------------
-->

@extends('layouts.layout')
<!-- Étend le layout principal défini dans resources/views/layouts/layout.blade.php -->

@section('title', 'Description du Service : ' . $service->name)
<!-- Définit le titre dynamique de la page en utilisant le nom du service -->

@section('content')
    <!--
    ==================================================================================================
    CONTAINER PRINCIPAL
    ==================================================================================================
    Conteneur Bootstrap pour structurer le contenu de la page.
    -->
    <div class="container">
        <!--
        --------------------------------------------------------------------------------------------------
        SECTION DESCRIPTION DU SERVICE
        --------------------------------------------------------------------------------------------------
        Carte avec effet glassmorphisme pour afficher le nom et la description du service.
        mb-4 : marge basse de 1.5rem pour espacer la section suivante.
        -->
        <div class="service-card mb-4">
            <!-- Titre principal de niveau 1 avec le nom du service -->
            <h1>Description du service : {{ $service->name }}</h1>

            <!-- Paragraphe avec la description complète du service -->
            <p>{{ $service->description }}</p>
        </div>

        <!--
        --------------------------------------------------------------------------------------------------
        GALERIE D'IMAGES
        --------------------------------------------------------------------------------------------------
        Grille responsive (3 colonnes) pour afficher des images illustratives du service.
        mb-4 : marge basse pour espacer la section suivante.
        -->
        <div class="row mb-4">
            <!--
            ----------------------------------------------------------------------------------------------
            COLONNE 1/3 - IMAGE 1
            ----------------------------------------------------------------------------------------------
            col-md-4 : occupe 4/12 colonnes sur écrans moyens/grands (33% de largeur)
            img-fluid : image responsive qui s'adapte à la largeur du conteneur
            -->
            <div class="col-md-4">
                <!--
                Image avec fallback vers un placeholder si l'image n'est pas définie
                $service->image1 ?? 'https://via.placeholder.com/300' : opérateur null coalescent
                -->
                <img src="{{ $service->image1 ?? 'https://via.placeholder.com/300' }}" class="img-fluid" alt="{{ $service->name }}">
            </div>

            <!--
            ----------------------------------------------------------------------------------------------
            COLONNE 2/3 - IMAGE 2
            ----------------------------------------------------------------------------------------------
            -->
            <div class="col-md-4">
                <img src="{{ $service->image2 ?? 'https://via.placeholder.com/300' }}" class="img-fluid" alt="{{ $service->name }}">
            </div>

            <!--
            ----------------------------------------------------------------------------------------------
            COLONNE 3/3 - IMAGE 3
            ----------------------------------------------------------------------------------------------
            -->
            <div class="col-md-4">
                <img src="{{ $service->image3 ?? 'https://via.placeholder.com/300' }}" class="img-fluid" alt="{{ $service->name }}">
            </div>
        </div>

        <!--
        --------------------------------------------------------------------------------------------------
        MOTEUR DE RECHERCHE PAR RÉGION
        --------------------------------------------------------------------------------------------------
        Permet de rechercher des prestataires proposant ce service dans une région spécifique.
        -->
        <div class="service-card mb-4">
            <!-- Titre de niveau 3 pour la section -->
            <h3>Rechercher ce service dans une région</h3>

            <!--
            ----------------------------------------------------------------------------------------------
            FORMULAIRE DE RECHERCHE
            ----------------------------------------------------------------------------------------------
            action="{ { route('service.recherche') } }" : soumet à la route nommée 'service.recherche'
            method="GET" : utilise la méthode HTTP GET pour la recherche
            class="input-group" : groupe le champ et le bouton horizontalement
            -->
            <form action="{{ route('service.recherche') }}" method="GET" class="input-group">
                <!--
                Champ caché pour transmettre l'ID du service
                name="service_id" : nom du paramètre qui sera passé à la route
                value="{{ $service->id }}" : ID du service actuel
                -->
                <input type="hidden" name="service_id" value="{{ $service->id }}">

                <!--
                Champ de texte pour la région
                name="region" : nom du paramètre qui sera passé à la route
                placeholder : exemple de valeurs attendues
                required : rend le champ obligatoire
                -->
                <input type="text" class="form-control" name="region" placeholder="Ex: Liège" required>

                <!-- Bouton de soumission du formulaire -->
                <button class="btn btn-primary" type="submit">Rechercher</button>
            </form>
        </div>

        <!--
        ==================================================================================================
        LISTE DES PRESTATAIRES
        ==================================================================================================
        Affiche les prestataires proposant ce service, avec pagination.
        -->
        @if(isset($prestataires) && count($prestataires) > 0)
            <!--
            --------------------------------------------------------------------------------------------------
            CONTAINER DES PRESTATAIRES
            --------------------------------------------------------------------------------------------------
            service-card : applique le style glassmorphisme
            mb-4 : marge basse pour espacer
            -->
            <div class="service-card mb-4">
                <!-- Titre dynamique incluant éventuellement la région -->
                <h3>Prestataires proposant ce service @if($region)à {{ $region }}@endif</h3>

                <!--
                ----------------------------------------------------------------------------------------------
                GRILLE DES PRESTATAIRES
                ----------------------------------------------------------------------------------------------
                row : conteneur flexible Bootstrap
                -->
                <div class="row">
                    <!--
                    ------------------------------------------------------------------------------------------
                    BOUCLE SUR LES PRESTATAIRES
                    ------------------------------------------------------------------------------------------
                    @ foreach : parcourt la collection $prestataires
                    -->
                    @foreach($prestataires as $prestataire)
                        <!--
                        --------------------------------------------------------------------------------------
                        COLONNE INDIVIDUELLE (1/3 de largeur)
                        --------------------------------------------------------------------------------------
                        col-md-4 : 4/12 colonnes sur écrans moyens/grands
                        mb-3 : marge basse pour espacer verticalement
                        -->
                        <div class="col-md-4 mb-3">
                            <!--
                            ----------------------------------------------------------------------------------
                            LIEN CLIQUABLE VERS LA PAGE DU PRESTATAIRE
                            ----------------------------------------------------------------------------------
                            text-decoration-none : supprime le soulignement du lien
                            href="{ { route('prestataire.details', ['id' => $prestataire->id]) } }" : route vers la page de détail
                            -->
                            <a href="{{ route('prestataire.details', ['id' => $prestataire->id]) }}" class="text-decoration-none">
                                <!--
                                ------------------------------------------------------------------------------
                                CARTE CLIQUABLE (clickable-card)
                                ------------------------------------------------------------------------------
                                h-100 : hauteur à 100% pour uniformité
                                clickable-card : applique les styles glassmorphisme et effets de survol
                                -->
                                <div class="card h-100 clickable-card">
                                    <!-- Corps de la carte avec les informations -->
                                    <div class="card-body">
                                        <!--
                                        --------------------------------------------------------------------------
                                        NOM DU PRESTATAIRE
                                        --------------------------------------------------------------------------
                                        card-title : style Bootstrap pour les titres de carte
                                        -->
                                        <h5 class="card-title">{{ $prestataire->nom }}</h5>

                                        <!--
                                        --------------------------------------------------------------------------
                                        INFORMATIONS DU PRESTATAIRE
                                        --------------------------------------------------------------------------
                                        card-text : style Bootstrap pour le texte des cartes
                                        -->
                                        <p class="card-text">
                                            <!-- Adresse avec icône (conditionnelle) -->
                                            @if($prestataire->adresse)
                                                <i class="bi bi-geo-alt"></i> {{ $prestataire->adresse }}<br>
                                            @endif

                                            <!-- Téléphone avec icône (conditionnelle) -->
                                            @if($prestataire->mobile_phone)
                                                <i class="bi bi-telephone"></i> {{ $prestataire->mobile_phone }}<br>
                                            @endif

                                            <!-- Site web avec icône (conditionnelle) -->
                                            @if($prestataire->website)
                                                <i class="bi bi-globe"></i> <span>{{ $prestataire->website }}</span><br>
                                            @endif

                                            <!-- Indicateur visuel que la carte est cliquable -->
                                        <div class="text-end mt-2">
                                            <i class="bi bi-arrow-right-circle"></i> Voir le profil
                                        </div>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!--
            ==================================================================================================
            ÉTAT VIDE (AUCUN PRESTATAIRE TROUVÉ)
            ==================================================================================================
            -->
            <div class="alert alert-info">
                <!-- Message conditionnel incluant éventuellement la région -->
                Aucun prestataire trouvé@if($region) dans la région {{ $region }}@endif.
            </div>
        @endif

        <!--
        --------------------------------------------------------------------------------------------------
        BOUTON DE RETOUR
        --------------------------------------------------------------------------------------------------
        btn btn-primary : style Bootstrap pour les boutons principaux
        mt-3 : marge haute de 1rem
        -->
        <a href="{{ route('home') }}" class="btn btn-primary mt-3">Retour à la page d'accueil</a>
    </div>

    <!--
    ==================================================================================================
    STYLES CSS INTÉGRÉS
    ==================================================================================================
    Styles spécifiques pour les cartes cliquables avec effet glassmorphisme.
    -->
    <style>
        /*
        --------------------------------------------------------------------------------------------------
        STYLE DES CARTES CLIQUABLES
        --------------------------------------------------------------------------------------------------
        Effet glassmorphisme avec :
        - Fond semi-transparent
        - Flou de l'arrière-plan (backdrop-filter)
        - Bordure subtile
        - Ombre légère
        */
        .clickable-card {
            transition: all 0.3s ease; /* Animation fluide pour les effets de survol */
            border: 1px solid rgba(255, 255, 255, 0.3); /* Bordure blanche semi-transparente */
            border-radius: 16px; /* Coins arrondis */
            background: rgba(255, 255, 255, 0.3); /* Fond blanc semi-transparent */
            backdrop-filter: blur(12px); /* Effet de flou pour l'effet verre */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Ombre légère pour le relief */
            height: 100%; /* Hauteur à 100% du conteneur parent */
        }

        /*
        --------------------------------------------------------------------------------------------------
        EFFETS AU SURVOL DES CARTES
        --------------------------------------------------------------------------------------------------
        */
        .clickable-card:hover {
            transform: translateY(-5px); /* Déplacement vers le haut pour effet "lévitation" */
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15); /* Ombre plus marquée */
            background: rgba(255, 255, 255, 0.4); /* Fond moins transparent */
            cursor: pointer; /* Curseur "main" pour indiquer le clic possible */
        }

        /*
        --------------------------------------------------------------------------------------------------
        STYLE DES LIENS (SANS SOULIGNEMENT)
        --------------------------------------------------------------------------------------------------
        */
        .text-decoration-none {
            text-decoration: none !important; /* Supprime le soulignement par défaut */
            color: inherit; /* Hérite de la couleur du texte parent */
        }

        /*
        --------------------------------------------------------------------------------------------------
        STYLE SPÉCIFIQUE DU CORPS DE CARTE
        --------------------------------------------------------------------------------------------------
        */
        .clickable-card .card-body {
            color: #212529; /* Couleur de texte foncée pour un bon contraste */
            padding: 1rem; /* Espacement interne */
        }

        /*
        --------------------------------------------------------------------------------------------------
        STYLE DU TITRE DE CARTE
        --------------------------------------------------------------------------------------------------
        */
        .clickable-card .card-title {
            color: #0d6efd; /* Couleur bleue primaire pour les titres */
            margin-bottom: 0.75rem; /* Marge basse réduite */
        }

        /*
        --------------------------------------------------------------------------------------------------
        STYLE DE L'ICÔNE DE FLÈCHE
        --------------------------------------------------------------------------------------------------
        */
        .bi-arrow-right-circle {
            color: #0d6efd; /* Couleur bleue pour correspondre aux liens */
            font-size: 1.2rem; /* Taille légèrement agrandie */
            margin-left: 0.5rem; /* Marge gauche pour espacer */
        }

        /*
        --------------------------------------------------------------------------------------------------
        STYLE DES BADGES
        --------------------------------------------------------------------------------------------------
        */
        .badge {
            margin-right: 0.3rem; /* Marge droite pour espacer les badges */
            margin-bottom: 0.3rem; /* Marge basse pour espacer les badges */
            font-size: 0.85rem; /* Taille de police légèrement réduite */
            padding: 0.35em 0.65em; /* Espacement interne */
        }

        /*
        --------------------------------------------------------------------------------------------------
        AJUSTEMENTS RESPONSIVE
        --------------------------------------------------------------------------------------------------
        */
        @media (max-width: 767.98px) {
            .col-md-4 {
                flex: 0 0 100%; /* Pleine largeur sur mobile */
                max-width: 100%; /* Largeur maximale */
            }
        }
        .bg-secondary {
            background-color: #48D1CC !important;
            color: white;
        }

        .badge {
            background-color: #48D1CC !important;
            color: white;
        }

        .badge bg-secondary text-decoration-none {
            background-color: #48D1CC !important;
            color: white;
        }

    </style>
@endsection
<!-- Fin de la section content -->
