<!--
====================================================================================================
FICHIER : resources/views/about.blade.php
----------------------------------------------------------------------------------------------------
Description : Page "À propos" pour la plateforme Bien-Être.
              Présente la mission, l'équipe et les valeurs du projet.
----------------------------------------------------------------------------------------------------
-->

@extends('layouts.layout')
<!-- Étend le layout principal défini dans resources/views/layouts/layout.blade.php -->

@section('title', 'À propos | Espace Bien-Être')
<!-- Définit le titre de la page avec le format "Nom | Site" pour le SEO -->

@section('content')
    <!--
    ==================================================================================================
    CONTAINER PRINCIPAL
    ==================================================================================================
    Conteneur avec padding vertical (py-4) pour espacer le contenu des éléments fixes (header/footer)
    -->
    <div class="container py-4">
        <!--
        --------------------------------------------------------------------------------------------------
        CARTE DE CONTENU PRINCIPAL (service-card)
        --------------------------------------------------------------------------------------------------
        text-start : aligne le texte à gauche (par défaut dans Bootstrap, mais explicite pour clarté)
        -->
        <div class="service-card text-start">
            <!--
            --------------------------------------------------------------------------------------------------
            TITRE PRINCIPAL
            --------------------------------------------------------------------------------------------------
            mb-4 : marge basse de 1.5rem pour espacer le contenu suivant
            bi bi-info-circle : icône "information" de Bootstrap Icons
            me-2 : marge droite de 0.5rem pour espacer l'icône du texte
            -->
            <h1 class="mb-4"><i class="bi bi-info-circle me-2"></i> À propos de nous</h1>

            <!--
            --------------------------------------------------------------------------------------------------
            GRILLE RESPONSIVE (2 colonnes)
            --------------------------------------------------------------------------------------------------
            col-md-8 : 8/12 colonnes pour le contenu textuel (2/3 de la largeur sur écrans moyens/grands)
            col-md-4 : 4/12 colonnes pour la carte de présentation (1/3 de la largeur sur écrans moyens/grands)
            -->
            <div class="row">
                <!--
                --------------------------------------------------------------------------------------------------
                COLONNE DE CONTENU TEXTE (gauche)
                --------------------------------------------------------------------------------------------------
                -->
                <div class="col-md-8">
                    <!--
                    ----------------------------------------------------------------------------------------------
                    PARAGRAPHE D'INTRODUCTION
                    ----------------------------------------------------------------------------------------------
                    lead : classe Bootstrap pour un texte légèrement plus grand et avec plus d'espacement
                    -->
                    <p class="lead">
                        Bienvenue sur <strong>Espace Bien-Être</strong>, une plateforme dédiée à votre équilibre physique et mental.
                        Créé par <strong>Mickael Collings</strong>, ce projet est né d'une passion pour le bien-être et la technologie.
                    </p>

                    <!--
                    ----------------------------------------------------------------------------------------------
                    SECTION "NOTRE MISSION"
                    ----------------------------------------------------------------------------------------------
                    mt-4 : marge haute de 1.5rem pour espacer des sections
                    -->
                    <h3 class="mt-4">Notre mission</h3>
                    <p>
                        Nous mettons à votre disposition des <strong>praticiens qualifiés</strong> et des ressources pour vous accompagner
                        vers un mieux-être au quotidien, que ce soit par le massage, le yoga, la méditation ou le coaching.
                    </p>

                    <!--
                    ----------------------------------------------------------------------------------------------
                    SECTION "NOTRE ÉQUIPE"
                    ----------------------------------------------------------------------------------------------
                    -->
                    <h3 class="mt-4">Notre équipe</h3>
                    <p>
                        Une équipe de passionnés, formés aux meilleures pratiques, pour vous offrir une expérience
                        <strong>personnalisée et professionnelle</strong>.
                    </p>

                    <!--
                    ----------------------------------------------------------------------------------------------
                    SECTION "NOS VALEURS"
                    ----------------------------------------------------------------------------------------------
                    -->
                    <h3 class="mt-4">Nos valeurs</h3>
                    <ul>
                        <!--
                        ------------------------------------------------------------------------------------------
                        VALEUR 1 : PROFESSIONNALISME
                        ------------------------------------------------------------------------------------------
                        Citation inspirée de votre phrase préférée : "No problem, we are professional"
                        -->
                        <li><strong>Professionnalisme</strong> : *« No problem, we are professional »*</li>

                        <!--
                        ------------------------------------------------------------------------------------------
                        VALEUR 2 : ACCESSIBILITÉ
                        ------------------------------------------------------------------------------------------
                        -->
                        <li><strong>Accessibilité</strong> : Des services pour tous, partout en Belgique</li>

                        <!--
                        ------------------------------------------------------------------------------------------
                        VALEUR 3 : INNOVATION
                        ------------------------------------------------------------------------------------------
                        -->
                        <li><strong>Innovation</strong> : Des outils modernes pour une expérience fluide</li>
                    </ul>
                </div>

                <!--
                --------------------------------------------------------------------------------------------------
                COLONNE DE PRÉSENTATION (droite)
                --------------------------------------------------------------------------------------------------
                -->
                <div class="col-md-4">
                    <!--
                    ----------------------------------------------------------------------------------------------
                    CARTE DE PRÉSENTATION DU FONDATEUR
                    ----------------------------------------------------------------------------------------------
                    -->
                    <div class="card">
                        <div class="card-body text-center">
                            <!--
                            --------------------------------------------------------------------------------------
                            ICÔNE DE PROFIL
                            --------------------------------------------------------------------------------------
                            bi bi-person-circle : icône "profil" de Bootstrap Icons
                            display-4 : taille de texte grande (équivalent à h4)
                            text-primary : couleur primaire (bleu) pour l'icône
                            mb-3 : marge basse de 1rem
                            -->
                            <i class="bi bi-person-circle display-4 text-primary mb-3"></i>

                            <!--
                            --------------------------------------------------------------------------------------
                            NOM DU FONDATEUR
                            --------------------------------------------------------------------------------------
                            card-title : style Bootstrap pour les titres de carte
                            -->
                            <h4 class="card-title">Mickael Collings</h4>

                            <!--
                            --------------------------------------------------------------------------------------
                            RÔLE
                            --------------------------------------------------------------------------------------
                            card-text : style Bootstrap pour le texte des cartes
                            -->
                            <p class="card-text">Fondateur et développeur</p>

                            <!--
                            --------------------------------------------------------------------------------------
                            DESCRIPTION PERSONNELLE
                            --------------------------------------------------------------------------------------
                            small : texte légèrement plus petit
                            -->
                            <p class="small">
                                Originaire d'<strong>Andrimont</strong> (près de Verviers), Mickael allie son expertise en
                                <strong>design graphique</strong> (IFAPME) et en <strong>développement web</strong>
                                (Institut Saint-Laurent, Liège) pour créer des solutions digitales sur mesure.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<!-- Fin de la section content -->
