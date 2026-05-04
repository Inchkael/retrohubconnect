<!--
====================================================================================================
FICHIER : resources/views/prestataires/show.blade.php
----------------------------------------------------------------------------------------------------
Description : Page de détail d'un prestataire pour la plateforme Bien-Être.
              Affiche les informations principales d'un prestataire de services.
----------------------------------------------------------------------------------------------------
-->

@extends('layouts.layout')
<!-- Étend le layout principal défini dans resources/views/layouts/layout.blade.php -->

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
        CARTE DE PRÉSENTATION DU PRESTATAIRE (service-card)
        --------------------------------------------------------------------------------------------------
        Utilise la classe service-card pour un style cohérent avec le reste de l'application
        (glassmorphisme, bordures arrondies, ombres légères)
        -->
        <div class="service-card">
            <!--
            ----------------------------------------------------------------------------------------------
            NOM DU PRESTATAIRE
            ----------------------------------------------------------------------------------------------
            Balise h2 pour le nom du prestataire (niveau de titre adapté pour une page de détail)
            -->
            <h2>{{ $prestataire->nom }}</h2>

            <!--
            ----------------------------------------------------------------------------------------------
            ADRESSE DU PRESTATAIRE
            ----------------------------------------------------------------------------------------------
            Balise <strong> pour mettre en évidence le label
            -->
            <p><strong>Adresse:</strong> {{ $prestataire->adresse }}</p>

            <!--
            ----------------------------------------------------------------------------------------------
            NUMÉRO DE TÉLÉPHONE
            ----------------------------------------------------------------------------------------------
            -->
            <p><strong>Téléphone:</strong> {{ $prestataire->telephone }}</p>

            <!--
            ----------------------------------------------------------------------------------------------
            ADRESSE EMAIL
            ----------------------------------------------------------------------------------------------
            -->
            <p><strong>Email:</strong> {{ $prestataire->email }}</p>

            <!--
            ----------------------------------------------------------------------------------------------
            DESCRIPTION DES SERVICES
            ----------------------------------------------------------------------------------------------
            Affiche la description complète du prestataire et de ses services
            -->
            <p><strong>Description:</strong> {{ $prestataire->description }}</p>
        </div>
    </div>
@endsection
<!-- Fin de la section content -->
