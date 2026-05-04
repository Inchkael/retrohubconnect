<!--
====================================================================================================
FICHIER : resources/views/contact.blade.php
----------------------------------------------------------------------------------------------------
Description : Page de contact pour la plateforme Bien-Être.
              Intègre un formulaire de contact, les coordonnées, les horaires et une carte Google Maps.
----------------------------------------------------------------------------------------------------
-->

@extends('layouts.layout')
<!-- Étend le layout principal défini dans resources/views/layouts/layout.blade.php -->

@section('title', 'Contact | Espace Bien-Être')
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
        text-start : aligne le texte à gauche pour une meilleure lisibilité
        -->
        <div class="service-card text-start">
            <!--
            --------------------------------------------------------------------------------------------------
            TITRE PRINCIPAL AVEC ICÔNE
            --------------------------------------------------------------------------------------------------
            mb-4 : marge basse de 1.5rem pour espacer le contenu suivant
            bi bi-envelope : icône "enveloppe" de Bootstrap Icons
            me-2 : marge droite de 0.5rem pour espacer l'icône du texte
            -->
            <h1 class="mb-4"><i class="bi bi-envelope me-2"></i> Contactez-nous</h1>

            <!--
            --------------------------------------------------------------------------------------------------
            GRILLE RESPONSIVE (2 colonnes)
            --------------------------------------------------------------------------------------------------
            -->
            <div class="row">
                <!--
                --------------------------------------------------------------------------------------------------
                COLONNE GAUCHE : FORMULAIRE DE CONTACT (col-md-6)
                --------------------------------------------------------------------------------------------------
                -->
                <div class="col-md-6">
                    <!--
                    ----------------------------------------------------------------------------------------------
                    TEXTE D'INTRODUCTION
                    ----------------------------------------------------------------------------------------------
                    lead : classe Bootstrap pour un texte légèrement plus grand et avec plus d'espacement
                    -->
                    <p class="lead">
                        Une question ? Besoin d'informations ?<br>
                        N'hésitez pas à nous contacter via le formulaire ci-dessous.
                    </p>

                    <!--
                    ----------------------------------------------------------------------------------------------
                    ALERTE DE SUCCÈS (masquée par défaut)
                    ----------------------------------------------------------------------------------------------
                    alert-success : style Bootstrap pour les messages de succès
                    d-none : classe Bootstrap pour masquer l'élément (display: none)
                    id="successAlert" : identifiant pour la manipulation JavaScript
                    -->
                    <div class="alert alert-success d-none" id="successAlert">
                        Votre message a été envoyé avec succès !
                    </div>

                    <!--
                    ----------------------------------------------------------------------------------------------
                    FORMULAIRE DE CONTACT
                    ----------------------------------------------------------------------------------------------
                    id="contactForm" : identifiant pour la manipulation JavaScript
                    action="{ { route('contact.send') } }" : soumet à la route nommée 'contact.send'
                    method="POST" : utilise la méthode HTTP POST
                    class="mt-4" : marge haute de 1.5rem
                    -->
                    <form id="contactForm" action="{{ route('contact.send') }}" method="POST" class="mt-4">
                        @csrf
                        <!-- Token CSRF pour la protection contre les attaques CSRF -->

                        <!--
                        ------------------------------------------------------------------------------------------
                        CHAMP NOM (obligatoire)
                        ------------------------------------------------------------------------------------------
                        mb-3 : marge basse de 1rem
                        form-label : style Bootstrap pour les labels
                        text-danger : couleur rouge pour l'astérisque (champ obligatoire)
                        form-control : style Bootstrap pour les champs de formulaire
                        id="name" et name="name" : identifiants pour le champ
                        required : attribut HTML5 pour rendre le champ obligatoire
                        -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <!--
                        ------------------------------------------------------------------------------------------
                        CHAMP EMAIL (obligatoire)
                        ------------------------------------------------------------------------------------------
                        type="email" : valide automatiquement le format email
                        -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <!--
                        ------------------------------------------------------------------------------------------
                        CHAMP SUJET (facultatif)
                        ------------------------------------------------------------------------------------------
                        -->
                        <div class="mb-3">
                            <label for="subject" class="form-label">Sujet</label>
                            <input type="text" class="form-control" id="subject" name="subject">
                        </div>

                        <!--
                        ------------------------------------------------------------------------------------------
                        CHAMP MESSAGE (obligatoire)
                        ------------------------------------------------------------------------------------------
                        textarea : champ de texte multi-lignes
                        rows="5" : hauteur de 5 lignes
                        -->
                        <div class="mb-3">
                            <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>

                        <!--
                        ------------------------------------------------------------------------------------------
                        BOUTON D'ENVOI
                        ------------------------------------------------------------------------------------------
                        btn btn-primary : style Bootstrap pour les boutons principaux
                        -->
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i> Envoyer
                        </button>
                    </form>
                </div>

                <!--
                --------------------------------------------------------------------------------------------------
                COLONNE DROITE : INFORMATIONS DE CONTACT (col-md-6)
                --------------------------------------------------------------------------------------------------
                -->
                <div class="col-md-6">
                    <!--
                    ----------------------------------------------------------------------------------------------
                    SECTION COORDONNÉES
                    ----------------------------------------------------------------------------------------------
                    list-unstyled : liste sans puces et sans marges latérales
                    -->
                    <h3><i class="bi bi-geo-alt me-2"></i> Nos coordonnées</h3>
                    <ul class="list-unstyled">
                        <!-- Téléphone -->
                        <li class="mb-2"><i class="bi bi-telephone me-2"></i> +32 4 123 45 67</li>
                        <!-- Email -->
                        <li class="mb-2"><i class="bi bi-envelope me-2"></i> contact@espace-bien-etre.be</li>
                        <!-- Adresse (Verviers, comme dans vos informations) -->
                        <li class="mb-2"><i class="bi bi-geo-alt me-2"></i> Rue de la Paix 123, 4800 Verviers, Belgique</li>
                    </ul>

                    <!--
                    ----------------------------------------------------------------------------------------------
                    SECTION HORAIRES
                    ----------------------------------------------------------------------------------------------
                    -->
                    <h3 class="mt-4"><i class="bi bi-clock me-2"></i> Horaires</h3>
                    <ul class="list-unstyled">
                        <li class="mb-2">Lundi - Vendredi : 9h - 18h</li>
                        <li class="mb-2">Samedi : 10h - 14h</li>
                        <li class="mb-2">Dimanche : Fermé</li>
                    </ul>

                    <!--
                    ----------------------------------------------------------------------------------------------
                    CARTE GOOGLE MAPS
                    ----------------------------------------------------------------------------------------------
                    ratio ratio-16x9 : conteneur avec ratio 16:9 pour la carte
                    mt-4 : marge haute de 1.5rem
                    iframe : intégration de Google Maps
                    allowfullscreen : permet l'affichage plein écran
                    loading="lazy" : chargement différé pour améliorer les performances
                    -->
                    <div class="ratio ratio-16x9 mt-4">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2520.3456789!2d5.851234!3d50.587654!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNTDCsDM1JzE3LjQiTiA1wrA1MScwMi4wIkU!5e0!3m2!1sfr!2sbe!4v1234567890" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--
    ==================================================================================================
    SCRIPT JAVASCRIPT POUR LE FORMULAIRE
    ==================================================================================================
    Gère l'affichage du message de succès après soumission du formulaire.
    -->
    <script>
        // Écouteur d'événement pour la soumission du formulaire
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            // Empêche la soumission normale du formulaire
            e.preventDefault();

            // Affiche l'alerte de succès en supprimant la classe 'd-none'
            document.getElementById('successAlert').classList.remove('d-none');
        });
    </script>
@endsection
<!-- Fin de la section content -->
