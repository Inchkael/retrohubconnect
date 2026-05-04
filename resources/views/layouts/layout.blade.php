
<!DOCTYPE html>
<!-- Déclaration du type de document : HTML5 -->
<html lang="{{ app()->getLocale() }}">
<!-- Langue du document -->
<head>
    @include('layouts.partials.head')
</head>

<body>

<script src="https://apis.google.com/js/platform.js?onload=initGoogle" async defer></script>

<!--
=========================================================================================
CONTENEUR PRINCIPAL (page-wrapper)
=========================================================================================
Div racine qui encapsule toute la structure de la page.
-->
<div class="page-wrapper">
    <div class="sticky-top-container">
        @include('layouts.partials.header')
        @include('layouts.partials.navbar')
    </div>

    <!--
    =========================================================================================
    LAYOUT PRINCIPAL (main-layout)
    =========================================================================================
    Conteneur flex qui organise la sidebar et le contenu principal.
    Cette structure est optimisée pour :
    - Un affichage responsive (mobile/desktop)
    - Une navigation intuitive (sidebar accessible)
    - Un contenu principal bien mis en valeur
    -->
    <div class="main-layout">
        @include('layouts.partials.sidebar')

        <!--
        =========================================================================================
        CONTENU PRINCIPAL
        =========================================================================================
        Zone dynamique où le contenu spécifique à chaque page sera inséré.
        - '@ yield('content')' : directive Blade qui permet aux vues enfants d'injecter leur contenu
        -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <!-- Pied de page -->
    @include('layouts.partials.footer')

    <!-- Boutons flottants -->

    @include('layouts.partials.floating-buttons')
    <!--
    =========================================================================================
    MODALES DE CONNEXION ET D'INSCRIPTION
    =========================================================================================
    -->
    @include('layouts.partials.modals')
<!--
=========================================================================================
SCRIPTS JAVASCRIPT (chargés en bas pour optimiser le chargement)
=========================================================================================
Les scripts sont placés en bas du body pour :
1. Permettre un chargement prioritaire du contenu HTML/CSS (meilleure expérience utilisateur)
2. Éviter les blocages du rendu
3. Respecter les bonnes pratiques de performance web
-->
<!--
Bootstrap JS avec intégrité (SRI - Subresource Integrity)
- 'integrity' : vérifie que le fichier n'a pas été altéré
- 'crossorigin="anonymous"' : permet le chargement depuis un CDN sans partager de cookies
- Cette version inclut Popper.js pour les tooltips et dropdowns
-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
<!--
=========================================================================================
SCRIPTS PERSONNALISÉS (pour la sidebar, la recherche vocale et les modales)
=========================================================================================
-->
<script>

    /**
     * Déconnecte l'utilisateur de Google, Facebook et soumet le formulaire Laravel.
     * Appelé lors du clic sur le bouton de déconnexion.
     */
    function logoutFromAll() {
        // 1. Déconnexion de Google (si gapi est chargé)
        if (typeof gapi !== 'undefined') {
            const auth2 = gapi.auth2.getAuthInstance();
            if (auth2) {
                auth2.signOut().then(() => {
                    console.log('Déconnexion de Google réussie.');
                }).catch((error) => {
                    console.error('Erreur Google:', error);
                });
            }
        }

    }

    /**
     * Initialise l'API Google après chargement du SDK.
     * Nécessaire pour gapi.auth2.getAuthInstance().
     */
    function initGoogle() {
        gapi.load('auth2', function() {
            gapi.auth2.init({
                client_id: '{{ env("GOOGLE_CLIENT_ID") }}',
            });
        });
    }



    // Écouteur d'événement qui se déclenche quand le DOM est complètement chargé
    document.addEventListener('DOMContentLoaded', function() {

        // =============================================================================
        // GESTION DE LA SIDEBAR (TOGGLE)
        // =============================================================================
        // Récupération des éléments DOM nécessaires
        const sidebarToggle = document.getElementById('sidebarToggle');      // Bouton de bascule
        const sidebarContainer = document.querySelector('.sidebar-container'); // Conteneur de la sidebar

        // Vérification que les éléments existent avant d'ajouter des écouteurs
        if (sidebarToggle && sidebarContainer) {
            // Ajout d'un écouteur d'événement pour le clic sur le bouton de bascule
            sidebarToggle.addEventListener('click', function() {
                // Bascule la classe 'show' sur le conteneur de la sidebar
                // Cette classe est définie dans le CSS pour afficher/masquer la sidebar
                sidebarContainer.classList.toggle('show');
            });
        }

        // =============================================================================
        // RECHERCHE VOCALE
        // =============================================================================
        // Récupération des éléments DOM pour la recherche vocale
        const voiceSearchButton = document.getElementById('voiceSearchButton'); // Bouton micro
        const searchInput = document.getElementById('searchInput');             // Champ de recherche

        // Vérification que les éléments existent avant de continuer
        if (voiceSearchButton && searchInput) {
            // =========================================================================
            // VÉRIFICATION DU SUPPORT DE L'API DE RECONNAISSANCE VOCALE
            // =========================================================================
            // Vérifie si l'API est disponible nativement dans le navigateur
            // Avec fallback pour Firefox via le polyfill chargé plus haut
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

            // Si l'API n'est pas supportée du tout (même avec polyfill)
            if (!SpeechRecognition) {
                // Désactive visuellement le bouton
                voiceSearchButton.style.opacity = 0.5;
                voiceSearchButton.title = "Recherche vocale non supportée";
                return; // Arrête l'exécution du script pour cette fonctionnalité
            }

            // =========================================================================
            // INITIALISATION DE LA RECONNAISSANCE VOCALE
            // =========================================================================
            // Crée une nouvelle instance de l'API de reconnaissance vocale
            const recognition = new SpeechRecognition();

            // Configuration de l'API
            recognition.lang = 'fr-FR';          // Langue française
            recognition.interimResults = false; // Ne retourne pas de résultats intermédiaires

            // =========================================================================
            // GESTION DES ÉTATS VISUELS (feedback utilisateur)
            // =========================================================================
            // Quand la reconnaissance vocale commence
            recognition.onstart = function() {
                // Ajoute une classe 'active' pour le style
                voiceSearchButton.classList.add('active');
                // Change l'icône pour indiquer que l'écoute est en cours
                voiceSearchButton.innerHTML = '<i class="bi bi-mic-mute-fill"></i>';
            };

            // Quand la reconnaissance vocale se termine
            recognition.onend = function() {
                // Retire la classe 'active'
                voiceSearchButton.classList.remove('active');
                // Rétablit l'icône originale
                voiceSearchButton.innerHTML = '<i class="bi bi-mic-fill"></i>';
            };

            // =========================================================================
            // TRAITEMENT DES RÉSULTATS DE LA RECONNAISSANCE
            // =========================================================================
            // Quand un résultat est obtenu
            recognition.onresult = function(event) {
                // Récupère le texte reconnu (en supprimant les espaces inutiles)
                const transcript = event.results[0][0].transcript.trim();
                // Place le texte dans le champ de recherche
                searchInput.value = transcript;
                // Soumet automatiquement le formulaire de recherche
                searchInput.form.submit();
            };

            // =========================================================================
            // GESTION DES ERREURS
            // =========================================================================
            // En cas d'erreur lors de la reconnaissance vocale
            recognition.onerror = function(event) {
                // Affiche l'erreur dans la console (pour le débogage)
                console.error("Erreur de reconnaissance vocale:", event.error);

                // Rétablit l'état visuel du bouton
                voiceSearchButton.classList.remove('active');
                voiceSearchButton.innerHTML = '<i class="bi bi-mic-fill"></i>';

                // Si l'erreur est due à un refus d'autorisation
                if (event.error === 'not-allowed') {
                    // Affiche une alerte pour informer l'utilisateur
                    alert("Veuillez autoriser l'accès au micro pour utiliser la recherche vocale.");
                }
            };

            // =========================================================================
            // ACTIVATION/DÉSACTIVATION DE LA RECONNAISSANCE
            // =========================================================================
            // Écouteur pour le clic sur le bouton de recherche vocale
            voiceSearchButton.addEventListener('click', function() {
                // Si le bouton est déjà actif (reconnaissance en cours)
                if (voiceSearchButton.classList.contains('active')) {
                    // Arrête la reconnaissance
                    recognition.stop();
                } else {
                    // Démarre la reconnaissance
                    recognition.start();
                }
            });
        }



        // =============================================================================
        // GESTION DES FORMULAIRES DE CONNEXION ET D'INSCRIPTION
        // =============================================================================
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(loginForm);

                fetch("{{ route('login.submit') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert("Erreur de connexion : " + (data.message || "Veuillez vérifier vos identifiants."));
                        }
                    })
                    .catch(error => console.error('Erreur:', error));
            });
        }

// =============================================================================
// GESTION DU FORMULAIRE D'INSCRIPTION (version optimisée pour Firefox et Bootstrap 5)
// =============================================================================
        const registerForm = document.getElementById('registerForm');
        if (registerForm) {
            registerForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const submitButton = this.querySelector('button[type="submit"]');

                // 1. Désactive le bouton et affiche un spinner
                submitButton.disabled = true;
                submitButton.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            En cours...
        `;

                // 2. Récupère les données du formulaire
                const formData = new FormData(this);

                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        // 3. Succès : affiche une popup SweetAlert2
                        await Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 2000
                        });

                        // 4. Ferme la modale d'inscription via Bootstrap Modal API
                        const registerModalElement = document.getElementById('registerModal');
                        const registerModal = bootstrap.Modal.getInstance(registerModalElement);
                        if (registerModal) {
                            registerModal.hide();
                        }

                        // 5. Réinitialise le formulaire
                        this.reset();

                        // 6. Redirige après un délai (pour laisser le temps à la popup de s'afficher)
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 2000);

                    } else {
                        // 7. Échec : affiche les erreurs dans une popup
                        let errorMessage = "Erreur lors de l'inscription:<br>";
                        if (data.errors) {
                            for (const field in data.errors) {
                                errorMessage += `<strong>${field}:</strong> ${data.errors[field].join('<br>')}<br>`;
                            }
                        } else {
                            errorMessage += data.message || "Veuillez vérifier vos informations.";
                        }

                        await Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            html: errorMessage,
                        });
                    }
                } catch (error) {
                    console.error('Erreur complète:', error);
                    await Swal.fire({
                        icon: 'error',
                        title: 'Erreur technique',
                        text: "Une erreur est survenue. Veuillez réessayer.",
                    });
                } finally {
                    // 8. Réactive TOUJOURS le bouton
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'S\'inscrire';
                }
            });
        }
    });
    // =============================================================================
    // GESTION DE LA RÉINITIALISATION DU MOT DE PASSE
    // =============================================================================
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(forgotPasswordForm);

            fetch("{{ route('password.forgot') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Un lien de réinitialisation a été envoyé à votre email");
                        forgotPasswordForm.reset();
                        const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                        loginModal.show();
                        const forgotPasswordModal = bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal'));
                        forgotPasswordModal.hide();
                    } else {
                        alert("Erreur : " + (data.message || "Adresse email non trouvé."));
                    }
                })
                .catch(error => console.error('Erreur:', error));
        });
    }

    /**
     * Déconnecte l'utilisateur de Google ET soumet le formulaire Laravel.
     * Appelé lors du clic sur le bouton de déconnexion.
     */
    function logoutFromAll() {
        // 1. Déconnexion de Google (si gapi est chargé)
        if (typeof gapi !== 'undefined' && gapi.auth2) {
            gapi.auth2.getAuthInstance().signOut()
                .then(() => {
                    console.log('Déconnexion de Google réussie.');
                })
                .catch((error) => {
                    console.error('Erreur lors de la déconnexion Google:', error);
                });
        }
    }

    /**
     * Initialise l'API Google après chargement du SDK.
     * Nécessaire pour gapi.auth2.getAuthInstance().
     */
    function initGoogle() {
        gapi.load('auth2', function() {
            gapi.auth2.init({
                client_id: '{{ env("GOOGLE_CLIENT_ID") }}',
            });
        });
    }

    /**
     * SweetAlert
     */

    document.getElementById('registerForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                // Popup de succès
                await Swal.fire({
                    icon: 'success',
                    title: 'Succès',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 2000
                });
                // Redirection après la popup
                window.location.href = data.redirect;
            } else {
                // Popup d'erreur
                await Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: data.message || 'Une erreur est survenue.',
                });
            }
        } catch (error) {
            await Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur est survenue lors de la requête.',
            });
        }
    });

    // Exemple pour gérer la réponse après une connexion Google
    window.handleGoogleCallback = async function() {
        try {
            const response = await fetch('/auth/google/callback', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Succès',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 2000
                });
                window.location.href = data.redirect;
            } else {
                await Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: data.message,
                });
            }
        } catch (error) {
            await Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur est survenue.',
            });
        }
    };



    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Connexion en cours...
        `;

            try {
                const formData = new FormData(this);
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();

                if (data.success) {
                    // Ferme la modale de connexion
                    const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
                    loginModal.hide();
                    // Affiche un message de succès
                    // Vérifie si l'utilisateur doit finaliser son inscription
                    if (data.needs_finalization) {
                        // Redirige vers la page de finalisation d'inscription
                        window.location.href = `/complete-registration?token=${data.token}`;
                    } else {
                        // Affiche un message de succès
                        window.location.href = data.redirect;
                    }
                } else {
                    // Affiche les erreurs de validation
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur de connexion',
                        text: data.message,
                    });
                }
            } catch (error) {
                console.error('Erreur complète:', error);
                // Affiche une erreur technique détaillée
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur technique',
                    text: 'Une erreur est survenue. Veuillez réessayer.',
                });
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Se connecter';
            }
        });
    }


</script>

<!--
=======================================================================================
OUTILS DE DEBUG (visible uniquement en développement et au survol)
=======================================================================================
-->
@if(app()->environment('local'))
    <!-- Bouton de debug discret -->
    <button id="debugToggleButton" style="
        position: fixed;
        bottom: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        border: none;
        border-radius: 6px 0 0 0;
        padding: 5px 10px;
        z-index: 9999;
        cursor: pointer;
        font-size: 0.7rem;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: all 0.3s ease;
    "
            title="Afficher les informations de debug">
        <i class="bi bi-bug"></i>
        <span>DEBUG</span>
    </button>

    <!-- Panneau de debug (masqué par défaut) -->
    @auth
        <div id="debugInfoPanel" style="
            position: fixed;
            bottom: 30px;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            padding: 10px 15px;
            z-index: 9998;
            border-radius: 8px 0 0 0;
            box-shadow: -3px -3px 8px rgba(0, 0, 0, 0.1);
            font-family: monospace;
            font-size: 0.8rem;
            max-width: 400px;
            border-left: 1px solid #eee;
            border-top: 1px solid #eee;
            display: none;
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        ">
            <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
                <span style="background: #0d6efd; color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem;">
                    DEBUG INFO
                </span>

                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    <span><strong>ID:</strong> {{ Auth::user()->id }}</span>
                    <span><strong>Rôle:</strong>
                        <span style="
                            padding: 2px 6px;
                            border-radius: 4px;
                            font-weight: bold;
                            color: white;
                            @if(Auth::user()->role === 'ADMIN')
                                background: #dc3545;
                            @elseif(Auth::user()->role === 'PROVIDER')
                                background: #28a745;
                            @elseif(Auth::user()->role === 'USER')
                                background: #0d6efd;
                            @else
                                background: #6c757d;
                            @endif
                        ">
                            {{ Auth::user()->role }}
                        </span>
                    </span>
                    <span><strong>Email:</strong> {{ Str::limit(Auth::user()->email, 20) }}</span>
                    <span><strong>Nom:</strong> {{ Auth::user()->last_name }}</span>
                    <span><strong>Confirmé:</strong>
                        @if(Auth::user()->is_confirmed)
                            <i class="bi bi-check-circle-fill" style="color: #28a745;"></i>
                        @else
                            <i class="bi bi-x-circle-fill" style="color: #dc3545;"></i>
                        @endif
                    </span>
                </div>
            </div>

            @if(Auth::user()->isProvider())
                <div style="margin-top: 8px; padding-top: 8px; border-top: 1px dashed #eee; font-size: 0.7rem;">
                    <strong>Infos Prestataire:</strong><br>
                    TVA: {{ Auth::user()->vat_number ?? 'Non renseigné' }} |
                    Téléphone: {{ Auth::user()->mobile_phone ?? 'Non renseigné' }} |
                    Site: {{ Str::limit(Auth::user()->website ?? 'Non renseigné', 20) }}
                </div>
            @endif
        </div>
    @endauth

    <!-- Script pour gérer l'affichage au survol -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const debugButton = document.getElementById('debugToggleButton');
            const debugPanel = document.getElementById('debugInfoPanel');

            if (debugButton && debugPanel) {
                // Afficher au survol du bouton
                debugButton.addEventListener('mouseenter', function() {
                    debugPanel.style.display = 'block';
                    setTimeout(() => {
                        debugPanel.style.opacity = '1';
                        debugPanel.style.transform = 'translateY(0)';
                    }, 10);
                });

                // Masquer quand la souris quitte le panneau ou le bouton
                debugButton.addEventListener('mouseleave', function(e) {
                    setTimeout(() => {
                        if (!debugPanel.matches(':hover')) {
                            debugPanel.style.opacity = '0';
                            debugPanel.style.transform = 'translateY(10px)';
                            setTimeout(() => {
                                debugPanel.style.display = 'none';
                            }, 300);
                        }
                    }, 200);
                });

                debugPanel.addEventListener('mouseleave', function() {
                    debugPanel.style.opacity = '0';
                    debugPanel.style.transform = 'translateY(10px)';
                    setTimeout(() => {
                        debugPanel.style.display = 'none';
                    }, 300);
                });
            }
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion de la copie dans le presse-papiers
            document.querySelectorAll('.copy-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const clipboardText = this.getAttribute('data-clipboard-text');
                    navigator.clipboard.writeText(clipboardText).then(() => {
                        const originalHtml = this.innerHTML;
                        this.innerHTML = '<i class="bi bi-check"></i> Copié!';
                        this.classList.replace('btn-outline-secondary', 'btn-outline-success');

                        setTimeout(() => {
                            this.innerHTML = originalHtml;
                            this.classList.replace('btn-outline-success', 'btn-outline-secondary');
                        }, 2000);
                    }).catch(err => {
                        console.error('Échec de la copie: ', err);
                    });
                });
            });

            // Gestion du téléchargement de l'avatar/logo
            const uploadAvatarBtn = document.getElementById('uploadAvatarBtn');
            if (uploadAvatarBtn) {
                uploadAvatarBtn.addEventListener('click', async function() {
                    const avatarInput = document.getElementById('avatar');
                    if (!avatarInput.files.length) {
                        await Swal.fire({
                            icon: 'warning',
                            title: 'Attention',
                            text: 'Veuillez sélectionner un fichier.',
                        });
                        return;
                    }

                    const formData = new FormData();
                    formData.append('avatar', avatarInput.files[0]);

                    const btn = this;
                    btn.disabled = true;
                    btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Téléchargement...`;

                    try {
                        const response = await fetch("{{ route('user.profile.upload_avatar') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: 'Succès',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            });

                            // Recharge la page pour afficher la nouvelle image
                            location.reload();
                        } else {
                            await Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: data.message || 'Une erreur est survenue.',
                            });
                        }
                    } catch (error) {
                        console.error('Erreur:', error);
                        await Swal.fire({
                            icon: 'error',
                            title: 'Erreur technique',
                            text: 'Une erreur est survenue. Veuillez réessayer.',
                        });
                    } finally {
                        btn.disabled = false;
                        btn.innerHTML = btn.getAttribute('data-original-text') || 'Mettre à jour l\'image';

                    }
                });
            }
        });

        // Gestion de la suppression des catégories
        document.addEventListener('DOMContentLoaded', function() {
            // Vérifiez que les boutons existent
            const deleteButtons = document.querySelectorAll('.delete-category-btn');

            if (deleteButtons.length > 0) {
                deleteButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        const categoryId = this.getAttribute('data-category-id');
                        const categoryName = this.getAttribute('data-category-name');
                        const route = this.getAttribute('data-route');

                        // Vérification supplémentaire
                        if (!route) {
                            console.error('Route non définie pour la suppression');
                            return;
                        }

                        Swal.fire({
                            title: 'Supprimer la catégorie?',
                            text: `Êtes-vous sûr de vouloir supprimer la catégorie "${categoryName}"? Cette action est irréversible et supprimera la catégorie pour tous les utilisateurs.`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Oui, supprimer!',
                            cancelButtonText: 'Annuler'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                deleteCategory(route, this);
                            }
                        });
                    });
                });
            }

            // Fonction pour supprimer une catégorie
            function deleteCategory(url, buttonElement) {
                const btn = buttonElement;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

                // Ajout de console.log pour le débogage
                console.log('URL de suppression:', url);
                console.log('Token CSRF:', document.querySelector('meta[name="csrf-token"]').content);

                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                    .then(response => {
                        console.log('Réponse brute:', response);
                        if (!response.ok) {
                            return response.json().then(err => {
                                console.error('Erreur dans la réponse:', err);
                                throw err;
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Réponse JSON:', data);
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Supprimé!',
                                text: data.message || 'La catégorie a été supprimée avec succès.',
                                showConfirmButton: false,
                                timer: 1500
                            });

                            // Supprimer visuellement la catégorie de la liste
                            const categoryElement = btn.closest('li.nav-item');
                            if (categoryElement) {
                                categoryElement.style.opacity = '0.5';
                                categoryElement.style.transition = 'all 0.3s ease';
                                setTimeout(() => {
                                    categoryElement.remove();
                                }, 300);
                            }
                        } else {
                            throw new Error(data.message || 'Erreur lors de la suppression');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur complète:', error);
                        let errorMessage = 'Une erreur est survenue lors de la suppression.';

                        if (error.message) {
                            errorMessage = error.message;
                        } else if (typeof error === 'object') {
                            errorMessage = JSON.stringify(error);
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: errorMessage,
                        });

                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-trash"></i>';
                    });
            }
        });

    </script>

@endif

<!-- jQuery (nécessaire pour certains plugins) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS (avec Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
        crossorigin="anonymous"></script>

<!-- Leaflet JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

<!-- Leaflet Routing Machine JS -->
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</body>
</html>
