<!--
====================================================================================================
FICHIER : resources/views/home.blade.php
----------------------------------------------------------------------------------------------------
Description : Page d'accueil pour la plateforme RetroHubConnect.

----------------------------------------------------------------------------------------------------
-->

@extends('layouts.layout')
<!-- Étend le layout principal défini dans resources/views/layouts/layout.blade.php -->

@section('content')
    <!--
    ==================================================================================================
    SLIDER DYNAMIQUE (Carousel Bootstrap)
    ==================================================================================================
    -->
    @include('slider')
    <!--
    ==================================================================================================
    ZONE DE SERVICE À LA UNE
    ==================================================================================================
    Section mettant en avant une offre spéciale du moment.
    -->
    @include('featured_service', ['monthService' => $monthService])

    <!--
    ==================================================================================================
    ZONE D'ARTICLE À LA UNE
    ==================================================================================================
    Section mettant en avant un article spéciale du moment.
    -->
    @include('home.featured_item', ['featuredItem' => $featuredItem])

    <!--
    ==================================================================================================
    ZONE DES DERNIERS ARTICLES
    ==================================================================================================
    Section mettant en avant les derniers articles.
    -->
    @include('home.last_items', ['lastItems' => $lastItems])


    <!--
   ==================================================================================================
   PARTENARIATS RÉCENTS
   ==================================================================================================
   -->
    @include('partenaires_recent', ['recentProviders' => $recentProviders])

    <!--
    ==================================================================================================
    LISTE PAGINÉE DES PRESTATAIRES
    ==================================================================================================
    Section pour afficher tous les prestataires avec pagination.
    mt-4 : marge haute pour espacer des éléments précédents
    -->
    <div class="row mt-4">
        <!--
        --------------------------------------------------------------------------------------------------
        COLONNE PLEINE LARGEUR
        --------------------------------------------------------------------------------------------------
        col-12 : occupe toute la largeur sur tous les écrans
        -->
        <div class="col-12">
            <!--
            ----------------------------------------------------------------------------------------------
            INCLUSION DE LA VUE PARTIELLE
            ----------------------------------------------------------------------------------------------
            @ include : directive Blade pour inclure un autre fichier de vue
            'providers.liste_paginee' : chemin vers la vue partielle qui gère l'affichage
                                      paginé des prestataires
            -->
            @include('providers.liste_paginee')
        </div>
    </div>
@endsection
<!-- Fin de la section content -->




