<!DOCTYPE html>
<!--
====================================================================================================
FICHIER : layout.blade.php - Layout principal pour l'application Bien-Être
----------------------------------------------------------------------------------------------------
Description : Template de base pour toutes les pages de la plateforme Bien-Être.
              Intègre une structure responsive avec sidebar et contenu principal.
              Conçu pour être étendu par les vues spécifiques via @ yield et @ section.
----------------------------------------------------------------------------------------------------
Compatibilité : Laravel 9+, Bootstrap 5.3, PHP 8.1+
-->

<head>
    <!--
    ----------------------------------------------------------------------------------------------------
    META TAGS ESSENTIELLES
    ----------------------------------------------------------------------------------------------------
    -->
    <meta charset="UTF-8">
    <!-- Définition de l'encodage des caractères pour une compatibilité internationale -->

    <title>Bien-être</title>
    <!-- Titre par défaut - Peut être écrasé par les vues enfants avec @ section('title') -->

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Configuration du viewport pour un design responsive (adaptation mobile) -->

    <!--
    ----------------------------------------------------------------------------------------------------
    FEUILLES DE STYLE EXTERNES
    ----------------------------------------------------------------------------------------------------
    -->
    <!-- Bootstrap 5.3 CSS via CDN pour une mise en page responsive et des composants prêts à l'emploi -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!--
    ----------------------------------------------------------------------------------------------------
    STYLES CSS INTÉGRÉS
    ----------------------------------------------------------------------------------------------------
    Styles de base pour la structure globale de l'application.
    -->
    <style>
        /*
        ------------------------------------------------------------------------------------
        STYLE DU BODY
        ------------------------------------------------------------------------------------
        */
        body {
            font-family: Arial, sans-serif; /* Police standard pour une bonne lisibilité */
            overflow-x: hidden; /* Désactive le défilement horizontal pour éviter les barres latérales indésirables */
        }

        /*
        ------------------------------------------------------------------------------------
        STYLE DE L'EN-TÊTE (header)
        ------------------------------------------------------------------------------------
        */
        header {
            background-color: #f8f9fa; /* Fond clair pour un contraste optimal avec le texte */
            padding: 20px; /* Espacement interne pour aérer le contenu */
            text-align: center; /* Centrage du contenu */
        }

        /*
        ------------------------------------------------------------------------------------
        STYLE DE LA BARRE LATÉRALE (sidebar)
        ------------------------------------------------------------------------------------
        */
        .sidebar {
            background-color: #f0f0f0; /* Fond gris clair pour distinguer la sidebar */
            padding: 20px; /* Espacement interne */
            height: calc(100vh - 80px); /* Hauteur = 100% de la viewport moins la hauteur de l'en-tête */
            position: fixed; /* Position fixe pour que la sidebar reste visible lors du défilement */
            width: 200px; /* Largeur fixe pour une sidebar cohérente */
        }

        /*
        ------------------------------------------------------------------------------------
        STYLE DU CONTENU PRINCIPAL (main-content)
        ------------------------------------------------------------------------------------
        */
        .main-content {
            margin-left: 220px; /* Marge gauche = largeur de la sidebar (200px) + padding (20px) */
            padding: 20px; /* Espacement interne pour le contenu principal */
        }

        /*
        ------------------------------------------------------------------------------------
        STYLE DES CARTES DE SERVICE (service-card)
        ------------------------------------------------------------------------------------
        */
        .service-card {
            border: 1px solid #ddd; /* Bordure légère pour délimiter la carte */
            border-radius: 5px; /* Coins légèrement arrondis pour un design moderne */
            padding: 20px; /* Espacement interne */
            margin-bottom: 20px; /* Marge basse pour espacer les cartes */
            text-align: center; /* Centrage du contenu */
        }

        /*
        ------------------------------------------------------------------------------------
        STYLE DU CONTAINER DE SLIDER (slider-container)
        ------------------------------------------------------------------------------------
        */
        .slider-container {
            margin: 20px 0; /* Marges verticales pour espacer le slider des autres éléments */
        }
    </style>
</head>

<body>
<!--
----------------------------------------------------------------------------------------------------
EN-TÊTE DE LA PAGE (header)
----------------------------------------------------------------------------------------------------
Section fixe en haut de page contenant le titre principal de la plateforme.
-->
<header>
    <h1>Bien-Être</h1>
    <!-- Titre principal - Peut être personnalisé dans les vues enfants si nécessaire -->
</header>

<!--
----------------------------------------------------------------------------------------------------
CONTAINER FLUID BOOTSTRAP
----------------------------------------------------------------------------------------------------
Conteneur plein largeur pour la mise en page principale utilisant le système de grille Bootstrap.
-->
<div class="container-fluid">
    <!--
    ------------------------------------------------------------------------------------
    ROW BOOTSTRAP
    ------------------------------------------------------------------------------------
    Ligne divisée en deux colonnes : sidebar (2/12) et contenu principal (10/12).
    -->
    <div class="row">
        <!--
        --------------------------------------------------------------------------------
        BARRE LATÉRALE (sidebar)
        --------------------------------------------------------------------------------
        Colonne occupant 2/12 de la largeur sur les écrans moyens et larges (col-md-2).
        Contient la navigation principale vers les catégories de services.
        -->
        <div class="sidebar col-md-2">
            <!-- Titre de la section des services -->
            <h3>Services proposés</h3>

            <!--
            ----------------------------------------------------------------------------
            LISTE DE NAVIGATION VERTICALE
            ----------------------------------------------------------------------------
            Utilise la classe 'flex-column' pour empiler les éléments verticalement.
            -->
            <ul class="nav flex-column">
                <!--
                -------------------------------------------------------------------------
                BOUCLE BLADE POUR LES CATÉGORIES DE SERVICES
                -------------------------------------------------------------------------
                Parcourt la collection $categories passée depuis le contrôleur.
                Pour chaque catégorie, crée un lien vers sa page dédiée.
                -->
                @foreach($categories as $category)
                    <!-- Élément de liste pour chaque catégorie -->
                    <li class="nav-item">
                        <!--
                        Lien vers la route 'service_categories.show' avec l'ID de la catégorie en paramètre.
                        La route est définie dans routes/web.php (ex: Route::get('/categories/{id}', ...)->name('service_categories.show'))
                        -->
                        <a class="nav-link" href="{{ route('service_categories.show', $category->id) }}">
                            {{ $category->name }} <!-- Affiche le nom de la catégorie -->
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <!--
            --------------------------------------------------------------------------------
            CONTENU PRINCIPAL (main-content)
            --------------------------------------------------------------------------------
            Colonne occupant 10/12 de la largeur sur les écrans moyens et larges (col-md-10).
            La directive @ yield('content') sera remplacée par le contenu des vues enfants.
            -->
        <div class="main-content col-md-10">
            @yield('content')
            <!--
                Section dynamique où les vues enfants (ex: home.blade.php, about.blade.php)
                pourront injecter leur contenu spécifique en utilisant @ section('content').
                -->
        </div>
    </div>
</div>

<!--
----------------------------------------------------------------------------------------------------
SCRIPTS JAVASCRIPT
----------------------------------------------------------------------------------------------------
-->
<!--
Bootstrap 5.3 JS Bundle avec Popper pour les composants interactifs (modales, dropdowns, etc.)
Inclut également les dépendances nécessaires comme Popper.js.
-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!--
    1. Ce layout doit être placé dans resources/views/layouts/layout.blade.php
    2. Les vues enfants doivent étendre ce layout avec @ extends('layouts.layout')
3. Le contrôleur doit passer la variable $categories à toutes les vues qui utilisent ce layout
   Exemple dans le contrôleur :
   public function index()
   {
       $categories = Category::all(); // Récupère toutes les catégories depuis la base de données
       return view('home', compact('categories'));
   }
4. Pour ajouter du contenu spécifique, les vues enfants doivent utiliser :
@section('content')
    // Contenu spécifique
@endsection
5. Pour personnaliser le titre :
@section('title')
    Titre personnalisé
@endsection
-->
</body>
</html>
