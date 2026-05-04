<style>
    /* ============================================= */
    /* 1. VARIABLES CSS GLOBALES (OPTIMISÉES) */
    /* ============================================= */
    :root {
        /* Dimensions */
        --header-height: 50px;
        --navbar-height: 60px;
        --footer-height: 0px;
        --sidebar-width: 250px;
        --sidebar-collapsed-width: 30px;
        --transition-speed: 0.5s;

        /* Couleurs (harmonisées avec le logo) */
        --primary-color: #2D95D5;             /* Bleu primaire */
        --secondary-color: #E1740D;           /* Orange secondaire */
        --tertiary-color: #374051;            /* Gris clair */
        --fourth-color : #222934;            /* Gris moyen */
        --fifth-color : #181D29;            /* Gris foncé */
        --sixth-color : #F8F7EE;            /* Blanc crème */


        --light-color: #E0F6FF;               /* Fond clair bleu pâle */
        --dark-color: #1A5252;                /* Vert foncé pour les textes */
        --text-color: #333;                   /* Couleur de texte standard */

        /* Effets glassmorphisme (optimisés) */
        --glassmorphism-bg: rgba(248, 249, 250, 0.7);
        --glassmorphism-border: rgba(255, 255, 255, 0.3);
        --glassmorphism-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);

        /* Transitions standardisées */
        --transition: all var(--transition-speed) ease;
        --transition-opacity: opacity var(--transition-speed) ease;
    }
    /* Cette section définit toutes les variables CSS utilisées dans le projet.
       Les dimensions ont été ajustées pour une meilleure cohérence visuelle (ex: header à 80px au lieu de 20px).
       Les couleurs sont inspirées du logo de l'entreprise avec des noms explicites.
       Les effets glassmorphisme sont optimisés pour un meilleur contraste et lisibilité.
       Les transitions sont standardisées pour une expérience utilisateur fluide. */

    /* ============================================= */
    /* 2. STYLES GLOBAUX (BODY) */
    /* ============================================= */
    body {
        font-family: 'Arial', sans-serif;
        overflow-x: hidden;
        background-color: var(--light-color);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        color: var(--text-color); /* Couleur de texte par défaut */
    }
    /* Configuration globale du corps de la page :
       - Police Arial avec fallback générique
       - Désactive le défilement horizontal pour éviter les barres de défilement indésirables
       - Fond clair bleu pâle défini par la variable --light-color
       - Hauteur minimale de 100% de la fenêtre (100vh)
       - Utilisation de Flexbox pour organiser le contenu en colonne
       - Padding supérieur pour éviter le chevauchement avec l'en-tête et la navbar fixes
       - Padding inférieur pour le pied de page
       - Couleur de texte standardisée */

    /* ============================================= */
    /* 3. EN-TÊTE (HEADER) */
    /* ============================================= */



    .retro-logo {
        max-height: 60px;
    }

    /* Barre de recherche centrée et large */
    .search-container {
        max-width: 33.33%; /* Largeur 1/3 de la page */
        margin: 0 auto; /* Centrage */
    }


    /* Conteneur qui englobe Header + Navbar */
    .sticky-top-container {
        position: -webkit-sticky; /* Support Safari */
        position: sticky;
        top: 0;
        z-index: 1050; /* Doit être au-dessus du reste du contenu */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); /* Ombre pour l'effet de profondeur au scroll */
    }

    /* Centrage du menu principal */
    .main-menu {
        width: 100%;
        display: flex;
        justify-content: center;
    }

    .main-menu ul {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1.5rem; /* Espace moderne entre les liens au lieu de marges manuelles */
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .main-menu a {
        color: var(--sixth-color); /* Utilisation de votre variable blanc crème */
        text-decoration: none;
        font-size: 1rem;
        transition: var(--transition); /* Utilisation de votre variable de transition */
        white-space: nowrap; /* Évite que les liens ne passent à la ligne */
    }

    .main-menu a:hover {
        color: var(--secondary-color); /* Orange au survol */
        transform: translateY(-2px);
        display: inline-block;
    }

    .retro-search-input {
        border-radius: 4px 0 0 4px;
        border: none;
        padding: 0.5rem;
        background-color: rgba(255, 255, 255, 0.9);
    }

    .btn-retro {
        background-color: #FF6600;
        color: white;
        border: none;
        border-radius: 0 4px 4px 0;
        padding: 0.5rem 1rem;
    }

    .btn-retro:hover {
        background-color: #e65c00;
    }

    .user-actions {
        margin-left: 0.5rem;
    }







    header {
        background-color: var(--primary-color);
        backdrop-filter: blur(10px);
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1020;
    }
    /* Style de l'en-tête :
       - Fond avec dégradé semi-transparent et image de fond
       - Effet de flou (backdrop-filter) pour le style glassmorphisme
       - Position fixe en haut de la page avec une hauteur définie par la variable
       - Z-index élevé pour s'assurer qu'il reste au-dessus des autres éléments
       - Ombre légère pour le relief */

    .header-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        padding: 0 15px;
    }
    /* Conteneur du contenu de l'en-tête :
       - Utilisation de Flexbox pour un centrage parfait
       - Direction colonne pour empiler les éléments verticalement
       - Alignement centré à la fois horizontalement et verticalement
       - Prend toute la hauteur disponible
       - Padding horizontal pour éviter que le contenu ne touche les bords */

    .header-content .row {
        width: 100%;
        max-width: 1600px;
        margin: 0 auto;
    }
    /* Style des lignes dans l'en-tête :
       - Largeur maximale de 1600px pour éviter l'étirement sur les grands écrans
       - Centré horizontalement avec margin auto */


    .header-content img {
        max-height: 130px;
        width: auto;
        border-radius: 10px;
        object-fit: contain;
    }

    .navbar-toggler {
        z-index: 9999; /* Valeur supérieure à celle de la navbar (1010) et de la sidebar (1000) dans ton code */
        position: relative; /* Assure que le z-index fonctionne */
    }

    /* Style du logo :
       - Hauteur maximale réduite à 80px pour une meilleure intégration
       - Largeur automatique pour conserver les proportions
       - Coins arrondis pour un look moderne
       - object-fit: contain pour s'assurer que l'image reste bien proportionnée */
    .language-selector {
        position: relative;
        display: inline-flex; /* Changement de inline-block à inline-flex pour un meilleur alignement */
        align-items: center;
        /* width: 100%;  <-- Supprimez ou commentez ceci pour éviter que le sélecteur ne s'étale */
    }

    .language-selector__button {
        display: flex;
        align-items: center;
        background: transparent;
        border: none;
        border-radius: 4px;
        padding: 6px 10px; /* Réduit de 11px 12px pour être plus compact */
        cursor: pointer;
        transition: all 0.3s ease;
        outline: none;
    }


    .language-selector__button:hover {
        background-color: #f8f9fa;

    }

    .language-selector__flag {
        width: 20px;
        height: auto;
        margin-right: 8px;
    }

    .language-selector__text {
        font-weight: 500;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .language-selector__icon {
        margin-left: 8px;
        font-size: 12px;
    }

    .language-selector__dropdown {
        position: absolute;
        top: 100%;
        left: 50%; /* On centre le dropdown par rapport au bouton */
        transform: translateX(-50%) translateY(10px); /* Centrage horizontal */
        width: 80px; /* RÉDUCTION : Passage de 200px à 80px */
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
        margin-top: 5px;
        padding: 0; /* Assure qu'aucun padding interne n'élargit le bloc */
    }

    .language-selector:hover .language-selector__dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(0); /* On garde le centrage horizontal lors de l'affichage */
    }

    .language-selector__item {
        list-style: none;
    }

    .language-selector__link {
        display: flex;
        align-items: center;
        justify-content: center; /* Centre le drapeau et le texte à l'intérieur du petit bloc */
        padding: 8px 5px; /* Padding réduit pour le menu compact */
        text-decoration: none;
        color: #333;
        transition: all 0.2s ease;
    }

    .language-selector__link:hover {
        background-color: #f8f9fa;
    }
    /* Responsive pour l'en-tête */
    @media (max-width: 767.98px) {
        .header-content .row {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .header-content .col-md-2,
        .header-content .col-md-7,
        .header-content .col-md-3 {
            width: 100%;
            margin-bottom: 1rem;
        }
    }
    /* Adaptation pour mobile :
       - Les colonnes s'empilent verticalement
       - Tout le contenu est centré
       - Chaque colonne prend toute la largeur disponible
       - Marges inférieures pour espacer les éléments */

    /* ============================================= */
    /* 4. BARRE DE NAVIGATION (NAVBAR) */
    /* ============================================= */
    .navbar {
        position: relative;
        width: 100%;
        height: var(--navbar-height);
        background-color: var(--tertiary-color);
        backdrop-filter: blur(8px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 0 15px;
        z-index: 999;
    }
    /* Style de la barre de navigation :
       - Position fixe juste en dessous de l'en-tête
       - Largeur complète de la page
       - Fond semi-transparent avec effet de flou
       - Ombre légère pour la séparation visuelle
       - Padding horizontal pour l'espacement
       - Z-index légèrement inférieur à celui de l'en-tête */

    /* ============================================= */
    /* 5. LAYOUT PRINCIPAL */
    /* ============================================= */
    .page-wrapper {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        position: relative;
    }
    /* Conteneur principal de la page :
       - Flexbox pour organiser les éléments enfants
       - Direction colonne pour un empilement vertical
       - Hauteur minimale de 100% de la fenêtre
       - Position relative pour permettre le positionnement absolu des enfants */

    .main-layout {
        display: flex;
        flex: 1;
        min-height: calc(100vh - var(--header-height) - var(--navbar-height) - var(--footer-height));
    }
    /* Layout principal :
       - Flexbox pour organiser la sidebar et le contenu principal
       - Prend tout l'espace disponible (flex: 1)
       - Hauteur minimale calculée pour éviter les chevauchements avec l'en-tête, la navbar et le pied de page */

    /* ============================================= */
    /* 6. BARRE LATÉRALE (SIDEBAR) */
    /* ============================================= */
    .sidebar-container {
        position: fixed;
        top: calc(var(--header-height) + var(--navbar-height));
        left: 0;
        z-index: 1000;
        width: var(--sidebar-collapsed-width);
        height: calc(100vh - var(--header-height) - var(--navbar-height) - var(--footer-height));
        transition: width var(--transition-speed) ease;
    }
    /* Conteneur de la sidebar :
       - Position fixe à gauche de l'écran
       - Positionné en dessous de l'en-tête et de la navbar
       - Largeur réduite par défaut (seulement l'icône de toggle visible)
       - Hauteur calculée pour remplir l'espace disponible
       - Transition fluide pour l'animation d'ouverture/fermeture */

    .sidebar-toggle {
        position: absolute;
        right: 0;
        width: var(--sidebar-collapsed-width);
        height: 60px;
        top: calc(50% - 80px);
        transform: translateY(-50%);
        background: rgba(0, 0, 0, 0.2);
        border-radius: 0 6px 6px 0;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition-opacity);
    }
    /* Bouton de toggle pour la sidebar :
       - Position absolue à droite du conteneur
       - Hauteur de 60px pour une bonne zone cliquable
       - Centré verticalement - 80px pour être à même hauteur que le toggle de droite
       - Fond semi-transparent noir
       - Coins arrondis seulement à droite
       - Curseur "pointer" pour indiquer l'interactivité
       - Transition pour l'effet de survol */

    .sidebar-toggle::after {
        content: "≡";
        color: white;
        font-size: 14px;
    }
    /* Icône du bouton toggle :
       - Symbole "hamburger" (≡) pour indiquer un menu
       - Couleur blanche pour contraste
       - Taille de 14px */

    .sidebar-container:hover .sidebar-toggle {
        background: rgba(0, 0, 0, 0.4);
    }
    /* Effet de survol sur le bouton toggle :
       - Fond plus foncé pour indiquer l'interaction */

    .sidebar {
        width: var(--sidebar-width);
        height: 100%;
        opacity: 0;
        transform: translateX(calc(-1 * var(--sidebar-width) + var(--sidebar-collapsed-width)));
        transition: var(--transition-opacity), transform var(--transition-speed) ease;
        background: var(--glassmorphism-bg);
        backdrop-filter: blur(12px);
        border-right: 1px solid var(--glassmorphism-border);
        padding: 0;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        border-radius: 0 16px 16px 0;
        overflow: hidden;
    }
    /* Style de la sidebar elle-même :
       - Largeur complète définie par la variable
       - Opacité à 0 (invisible) par défaut
       - Position initiale décalée vers la gauche (masquée)
       - Fond semi-transparent avec effet glassmorphisme
       - Bordure droite légère pour la séparation
       - Ombre légère pour le relief
       - Coins arrondis seulement à droite
       - Contenu masqué si débordement */

    .sidebar-container:hover .sidebar {
        opacity: 1;
        transform: translateX(0);
    }
    /* Comportement au survol :
       - Devient complètement opaque
       - Se positionne normalement (non décalée) */

    .sidebar-header {
        background: rgba(240, 240, 240, 0.6);
        padding: 15px;
        position: sticky;
        top: 0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
    }
    /* En-tête de la sidebar :
       - Fond gris clair semi-transparent
       - Padding pour l'espacement interne
       - Position sticky pour rester visible lors du défilement
       - Ombre légère pour la séparation
       - Flexbox pour aligner le contenu */

    .sidebar-content {
        padding: 0 15px 15px;
        height: calc(100% - 50px); /* 50px pour l'en-tête */
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: var(--primary-color) rgba(240, 240, 240, 0.4);
    }
    /* Contenu de la sidebar :
       - Padding pour l'espacement (bas seulement)
       - Hauteur calculée pour tenir compte de l'en-tête
       - Défilement vertical si nécessaire
       - Barre de défilement fine et colorée */

    .sidebar-content::-webkit-scrollbar {
        width: 6px;
    }
    .sidebar-content::-webkit-scrollbar-thumb {
        background-color: var(--primary-color);
        border-radius: 10px;
    }
    /* Style de la barre de défilement pour les navigateurs WebKit :
       - Largeur de 6px pour une apparence discrète
       - Couleur de la poignée correspondant à la couleur primaire
       - Coins arrondis pour la poignée */

    /* ============================================= */
    /* 7. CONTENU PRINCIPAL */
    /* ============================================= */
    .main-content {
        margin-left: var(--sidebar-collapsed-width);
        background-color: var(--fourth-color);
        padding: 20px;
        transition: var(--transition);
        flex: 1;
        overflow-y: auto;
        min-height: 100%;
        width: calc(100% - var(--sidebar-collapsed-width));
    }
    /* Contenu principal :
       - Marge gauche pour éviter le chevauchement avec la sidebar réduite
       - Padding pour l'espacement interne
       - Transition fluide pour l'animation
       - Prend tout l'espace disponible (flex: 1)
       - Défilement vertical si nécessaire
       - Largeur calculée pour remplir l'espace disponible */

    .sidebar-container:hover ~ .main-content {
        margin-left: var(--sidebar-width);
        width: calc(100% - var(--sidebar-width));
    }
    /* Comportement quand la sidebar est visible :
       - Marge gauche augmentée pour la largeur complète de la sidebar
       - Largeur recalculée pour remplir l'espace restant */

    /* ============================================= */
    /* 8. CARTES DE SERVICES */
    /* ============================================= */
    .service-card {
        background: var(--glassmorphism-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glassmorphism-border);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 20px;
        text-align: center;
        box-shadow: var(--glassmorphism-shadow);
        transition: var(--transition);
        color: var(--dark-color);
    }
    /* Style des cartes de service :
       - Fond semi-transparent avec effet glassmorphisme
       - Bordure légère pour la définition
       - Coins très arrondis (16px)
       - Padding pour l'espacement interne
       - Marge inférieure pour espacer les cartes
       - Texte centré
       - Ombre légère pour le relief
       - Transition fluide pour les effets de survol
       - Couleur de texte foncée pour un bon contraste */

    .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        background: rgba(255, 255, 255, 0.6); /* 0.4 → 0.6 pour meilleur contraste */
    }
    /* Effets au survol :
       - Légère élévation (5px vers le haut)
       - Ombre plus marquée pour un effet 3D
       - Fond plus opaque pour améliorer la lisibilité */

    .clickable-card {
        transition: var(--transition);
        border: 1px solid var(--glassmorphism-border);
        border-radius: 16px;
        background: var(--glassmorphism-bg);
        backdrop-filter: blur(12px);
        box-shadow: var(--glassmorphism-shadow);
        height: 100%;
        color: var(--dark-color);
    }
    /* Style des cartes cliquables :
       - Même style que les cartes de service
       - Hauteur à 100% du conteneur parent
       - Couleur de texte foncée pour le contraste */

    .clickable-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        background: rgba(255, 255, 255, 0.6);
        cursor: pointer;
    }
    /* Effets au survol identiques aux cartes de service :
       - Même animation de levée
       - Curseur "pointer" pour indiquer l'interactivité */

    /* ============================================= */
    /* 9. BOUTONS FLOTTANTS */
    /* ============================================= */
    .floating-buttons-container {
        position: fixed;
        top: 50%;
        right: 0;
        transform: translateY(-50%);
        z-index: 1015;
        width: 50px;
    }
    /* Conteneur des boutons flottants :
       - Position fixe à droite de l'écran
       - Centré verticalement
       - Z-index élevé pour s'assurer qu'il reste au-dessus du contenu
       - Largeur de 50px pour accommoder les boutons */

    .floating-buttons-toggle {
        position: absolute;
        right: 0;
        top: 50%;
        width: var(--sidebar-collapsed-width);
        transform: translateY(-50%);
        height: 60px;
        background: rgba(0, 0, 0, 0.2);
        border-radius: 6px 0 0 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition-opacity);
    }
    /* Bouton de toggle pour les boutons flottants :
       - Position absolue à droite du conteneur
       - Hauteur de 60px pour une bonne zone cliquable
       - Fond semi-transparent noir
       - Coins arrondis à gauche seulement
       - Curseur "pointer" pour indiquer l'interactivité */

    .floating-buttons-toggle::after {
        content: "≡";
        color: white;
        font-size: 14px;
        transform: rotate(90deg);
    }
    /* Icône du bouton toggle :
       - Symbole "hamburger" tourné à 90° pour une orientation verticale
       - Couleur blanche pour contraste
       - Taille de 14px */

    .floating-buttons-container:hover .floating-buttons-toggle {
        background: rgba(0, 0, 0, 0.4);
    }
    /* Effet de survol sur le bouton toggle :
       - Fond plus foncé pour indiquer l'interaction */

    .floating-buttons {
        display: flex;
        flex-direction: column;
        gap: 10px;
        opacity: 0;
        transition: var(--transition-opacity), transform var(--transition-speed) ease;
        transform: translateX(40px);
    }
    /* Liste des boutons flottants :
       - Flexbox en colonne pour empiler les boutons verticalement
       - Espacement de 10px entre les boutons
       - Opacité à 0 (invisible) par défaut
       - Transition fluide pour l'animation
       - Position initiale décalée vers la droite (masquée) */

    .floating-buttons-container:hover .floating-buttons {
        opacity: 1;
        transform: translateX(0);
    }
    /* Comportement au survol :
       - Devient complètement opaque
       - Se positionne normalement (non décalée) */

    .floating-buttons .btn {
        width: 50px;
        height: 50px;
        color: var(--dark-color) !important;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50% !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        transition: var(--transition);
        background: white !important;
        border: 1px solid #dee2e6 !important;
    }
    /* Style des boutons individuels :
       - Forme ronde (50px × 50px)
       - Couleur de texte foncée
       - Contenu centré
       - Fond blanc
       - Bordure grise claire
       - Ombre légère pour le relief
       - Transition fluide pour les effets de survol */

    .floating-buttons .btn:hover {
        transform: scale(1.3);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        background: #f8f9fa !important;
    }
    /* Effets au survol :
       - Légère augmentation de taille (30%)
       - Ombre plus marquée pour un effet 3D
       - Fond légèrement gris pour indiquer l'interaction */

    /* ============================================= */
    /* 10. PIED DE PAGE (FOOTER) */
    /* ============================================= */
    footer {
        background: var(--fifth-color);
        color: white;
        padding: 20px 0;
        width: 100%;
        margin-top: auto;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 10;
    }
    /* Style du pied de page :
       - Fond avec dégradé horizontal entre les couleurs primaire et secondaire
       - Texte blanc pour un bon contraste
       - Padding vertical pour l'espacement
       - Largeur complète
       - Margin-top auto pour le pousser en bas
       - Ombre légère en haut pour la séparation
       - Position relative et z-index pour le superposer correctement */

    footer h5 {
        color: white;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }
    /* Style des titres dans le pied de page :
       - Couleur blanche
       - Marge inférieure pour espacer le contenu
       - Flexbox pour aligner les icônes et le texte */

    footer a {
        color: rgba(255, 255, 255, 0.9);
        transition: color 0.3s ease;
    }
    /* Style des liens dans le pied de page :
       - Couleur blanche semi-transparente
       - Transition fluide pour la couleur au survol */

    footer a:hover {
        color: white;
        text-decoration: underline;
    }
    /* Effets au survol des liens :
       - Couleur blanche complète
       - Soulignement pour indiquer l'interactivité */

    /* ============================================= */
    /* 11. BOUTON DE RECHERCHE VOCALE */
    /* ============================================= */
    #voiceSearchButton {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        border-radius: 0 4px 4px 0 !important;
        margin-left: -1px !important;
        border-color: #dee2e6 !important;
        background-color: #f8f9fa !important;
        height: 38px;
    }
    /* Style du bouton de recherche vocale :
       - Affichage inline-flex pour s'intégrer dans la barre de recherche
       - Contenu centré
       - Largeur minimale de 38px
       - Coins arrondis seulement à droite
       - Aligné avec la barre de recherche (margin-left négatif)
       - Bordure grise claire
       - Fond gris très clair
       - Hauteur fixe de 38px */

    /* ============================================= */
    /* 12. BOUTON DE CONNEXION GOOGLE */
    /* ============================================= */

    /* Style pour le bouton Google - Adapté au thème glassmorphisme */
    .google-auth-wrapper {
        z-index: 1030; /* Au-dessus du header */
    }

    .glassmorph-btn {
        backdrop-filter: blur(8px);
        background-color: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #4285F4; /* Bleu Google */
        transition: all 0.3s ease;
    }

    .glassmorph-btn:hover {
        background-color: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .glassmorph-btn i {
        color: #EA4335; /* Rouge Google */
    }

    /* ============================================= */
    /* 12. BOUTON DE DECONNEXION GOOGLE */
    /* ============================================= */

    /* Style pour le lien de déconnexion (identique aux autres nav-link) */
    .logout-link {
        cursor: pointer;
    }

    /* Assure que tous les nav-link ont le même style glassmorphisme */
    .navbar-nav .nav-link {
        backdrop-filter: blur(8px);
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 4px;
        transition: all 0.3s ease;
    }

    .navbar-nav .nav-link i {
        font-size: 1.2rem;
    }

    .navbar-nav .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.2);
        transform: scale(1.05);
    }

    /* Style pour l'icône du profil utilisateur/prestataire */
    .navbar-nav .nav-link[href*="user/profile"] i {
        color: var(--primary-color); /* Utilise la couleur primaire de votre thème */
    }

    /* Effet au survol */
    .navbar-nav .nav-link[href*="user/profile"]:hover i {
        color: var(--secondary-color); /* Couleur secondaire pour le survol */
    }


    /* Style pour la modale de connexion */
    .login-modal {
        backdrop-filter: blur(12px);
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        max-width: 500px;
        margin: 1.75rem auto;
    }
    .login-modal .modal-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }
    .login-modal .modal-title {
        color: var(--dark-color);
    }
    .login-modal .btn-social {
        width: 100%;
        margin-bottom: 10px;
        background-color: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(0, 0, 0, 0.1);
    }
    .login-modal .divider {
        text-align: center;
        margin: 15px 0;
        color: #6c757d;
    }
    .login-modal .divider::before {
        content: "ou";
        display: inline-block;
        background: white;
        padding: 0 10px;
    }
    .login-modal .divider::after {
        content: "";
        display: block;
        height: 1px;
        background: rgba(0, 0, 0, 0.1);
        margin-top: 8px;
    }
    .login-modal .form-control {
        background-color: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(0, 0, 0, 0.1);
    }
    .login-modal .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    /* Dans ta section <style> */
    .btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* Animation pour le spinner */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .spinner-border {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        vertical-align: text-bottom;
        border: 0.2em solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spin 0.75s linear infinite;
    }

    /* Version mobile */
    @media (max-width: 991.98px) {
        .navbar-nav .nav-link {
            border-radius: 4px;
            width: auto;
            height: auto;
            padding: 0.5rem 1rem;
        }

        .header-content img {
            max-height: 50px;
            width: auto;
            border-radius: 10px;
            object-fit: contain;
        }

    }

    /* Couleur spécifique pour Google (optionnel) */
    .navbar-nav .nav-link[href="{{ route('google.login') }}"] i {
        color: #4285F4;
    }

    .navbar-nav .nav-link[href="{{ route('google.login') }}"]:hover i {
        color: #34A853;
    }

    /* Couleur pour la déconnexion */
    .navbar-nav .logout-link i {
        color: #EA4335; /* Rouge Google pour cohérence */
    }

    .navbar-nav .logout-link:hover i {
        color: #FBBC05; /* Jaune Google au survol */
    }

    /* Style pour le lien de profil (utilisateurs et prestataires) */
    .navbar-nav .nav-link.profile-link {
        backdrop-filter: blur(8px);
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 4px;
        transition: all 0.3s ease;
    }

    .navbar-nav .nav-link.profile-link i {
        font-size: 1.2rem;
        color: var(--primary-color);
    }

    .navbar-nav .nav-link.profile-link:hover {
        background-color: rgba(255, 255, 255, 0.2);
        transform: scale(1.05);
    }

    .navbar-nav .nav-link.profile-link:hover i {
        color: var(--secondary-color);
    }

    /* Version mobile */
    @media (max-width: 991.98px) {
        .navbar-nav .nav-link.profile-link {
            border-radius: 4px;
            width: auto;
            height: auto;
            padding: 0.5rem 1rem;
        }
    }

    /* ============================================= */
    /* 12. BADGES */
    /* ============================================= */
    .badge {
        margin-right: 0.3rem;
        margin-bottom: 0.3rem;
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
        background-color: var(--tertiary-color);
        color: white;
    }
    /* Style des badges (étiquettes des services) :
       - Marges pour l'espacement
       - Taille de police réduite (85% de la taille normale)
       - Padding pour l'espacement interne
       - Fond bleu turquoise
       - Texte blanc pour un bon contraste */

    /* ============================================= */
    /* 13. FALLBACKS ET RESPONSIVE */
    /* ============================================= */
    @supports not (backdrop-filter: blur(10px)) {
        header, .sidebar, .service-card, footer {
            background: rgba(255, 255, 255, 0.9) !important;
        }
    }
    /* Fallback pour les navigateurs ne supportant pas backdrop-filter :
       - Fond semi-transparent blanc (90% d'opacité) comme solution de repli
       - !important pour forcer le style malgré d'autres déclarations */

    /* ============================================= */
    /* 14. STYLES POUR LE FORUM */
    /* ============================================= */

    .forum-card, .topic-card {
        transition: var(--transition);
        border-left: 4px solid var(--primary-color);
    }

    .forum-card:hover, .topic-card:hover {
        background: rgba(255, 255, 255, 0.8);
        border-left: 4px solid var(--secondary-color);
    }

    .topic-card {
        border-radius: 12px;
        padding: 25px;
    }

    .forum-card h3, .topic-card h4 {
        margin: 0;
        font-weight: 600;
    }

    .badge {
        background-color: var(--tertiary-color);
        color: white;
        font-size: 0.8rem;
        padding: 0.4em 0.8em;
        border-radius: 20px;
    }

    /* Style pour les réponses dans un sujet */
    .reply-card {
        background: var(--glassmorphism-bg);
        backdrop-filter: blur(8px);
        border: 1px solid var(--glassmorphism-border);
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: var(--glassmorphism-shadow);
    }

    .reply-card:hover {
        background: rgba(255, 255, 255, 0.6);
        transform: translateY(-3px);
    }

    /* Style pour le formulaire de réponse */
    .reply-form {
        background: var(--glassmorphism-bg);
        backdrop-filter: blur(8px);
        border: 1px solid var(--glassmorphism-border);
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
    }

    .reply-form textarea {
        width: 100%;
        min-height: 120px;
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        padding: 10px;
        resize: vertical;
    }

    .reply-form button[type="submit"] {
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 8px 20px;
        margin-top: 10px;
        cursor: pointer;
        transition: var(--transition);
    }

    .reply-form button[type="submit"]:hover {
        background: var(--secondary-color);
        transform: translateY(-2px);
    }

    .like-form {
        display: inline;
    }

    .btn-like {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        font-size: 1rem;
        color: inherit;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .btn-like:focus {
        outline: none;
    }

    .btn-like i {
        font-size: 1.1rem;
    }

    .reply-card {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        border-left: 4px solid #007bff;
    }

    blockquote {
        font-style: italic;
        color: #555;
        border-left: 3px solid #ccc;
        padding-left: 15px;
        margin: 10px 0;
        background-color: #f9f9f9;
        padding: 10px;
    }

    .EasyMDEContainer .CodeMirror-line > span {
        white-space: pre;
    }

    .EasyMDEContainer .CodeMirror-line {
        padding: 0 4px;
    }

    .EasyMDEContainer .CodeMirror-line > span:first-child {
        color: #666;
        font-style: italic;
    }

    /* Amélioration visuelle dans l'éditeur */
    .CodeMirror .cm-quote {
        color: #2c3e50 !important; /* Une couleur plus sombre */
        background-color: rgba(0, 0, 0, 0.03); /* Un léger fond gris */
        font-style: italic;
        display: inline-block;
        width: 100%;
    }

    /* Style de l'aperçu (Preview) */
    .editor-preview blockquote {
        border-left: 4px solid #ccc;
        padding-left: 15px;
        color: #666;
        background: #f9f9f9;
        padding: 10px;
        margin: 10px 0;
    }

    /* Style pour les citations dans les réponses affichées */
    .post-content blockquote {
        font-style: italic;
        color: #555;
        border-left: 3px solid #ccc;
        padding-left: 15px;
        margin: 10px 0;
        background-color: #f9f9f9;
        padding: 10px;
    }

    /* Style pour les cartes de réponse */
    .reply-card {
        border-left: 4px solid #dee2e6;
        background-color: #fff;
        padding: 15px;
        margin-bottom: 15px;
    }

    .reply-card:nth-child(even) {
        border-left-color: #0d6efd;
    }

    /* Style pour les avatars */
    .reply-card img {
        width: 50px;
        height: 50px;
        object-fit: cover;
    }

    /* Style pour le contenu des posts */
    .post-content {
        line-height: 1.6;
    }

    /* Style pour les badges */
    .badge {
        font-size: 0.9em;
        padding: 0.35em 0.65em;
    }

    <!-- CSS pour les citations -->
     .post-content blockquote {
         font-style: italic;
         color: #555;
         border-left: 3px solid #ccc;
         padding-left: 15px;
         margin: 10px 0;
         background-color: #f9f9f9;
     }

    .reply-card {
        border-left: 4px solid #dee2e6;
    }

    .reply-card:nth-child(even) {
        border-left-color: #0d6efd;
    }

     .rating-stars .form-check {
         cursor: pointer;
     }
    .rating-stars .form-check-input {
        position: absolute;
        opacity: 0;
    }
    .rating-stars .form-check-label {
        cursor: pointer;
        padding: 0.375rem 0.75rem;
    }

    /* Style pour le badge de notification sur les messages */
    .nav-link.position-relative .badge {
        top: 10%;
        left: 90%;
        transform: translate(-50%, -50%);
    }

    /* Style pour les dropdowns en mobile */
    @media (max-width: 991.98px) {
        .dropdown-menu-end {
            left: 0 !important;
            right: auto !important;
            width: 100%;
        }

        .nav-item.dropdown {
            position: static;
        }
    }

    /* Style pour les éléments du dropdown */
    .dropdown-item {
        padding: 0.5rem 1rem;
    }

    .dropdown-item.active, .dropdown-item:active {
        background-color: rgba(0, 0, 0, 0.05);
    }
</style>
