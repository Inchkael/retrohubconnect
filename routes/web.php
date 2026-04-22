<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\CommentaireController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\ForumTopicController;
use App\Http\Controllers\ForumLikeController;
use App\Http\Controllers\ForumCategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Routes organisées par groupe de middleware et préfixe pour une meilleure
| lisibilité et maintenabilité. Les noms de routes sont conservés pour
| éviter les ruptures de compatibilité.
|
*/

// =============================================================================
// PUBLIC ROUTES (accessibles à tous)
// =============================================================================

// Route principale pour la page d'accueil
// Méthode: GET, URL: /, Contrôleur: HomeController@index, Nom: home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Route pour enregistrer un commentaire (nécessite une authentification)
// Méthode: POST, URL: /commentaires, Contrôleur: CommentaireController@store, Nom: commentaires.store
// Middleware: auth (nécessite une authentification)
Route::post('/commentaires', [CommentaireController::class, 'store'])->name('commentaires.store')->middleware('auth');

// Routes pour les catégories de services (accès public en lecture seule)
// Préfixe: /service_categories
// Groupe de routes avec préfixe et nom de route commun
Route::prefix('service_categories')->name('service_categories.')->group(function () {
    // Liste des catégories de services
    // Méthode: GET, URL: /service_categories/, Contrôleur: ServiceCategoryController@index, Nom: service_categories.index
    Route::get('/', [ServiceCategoryController::class, 'index'])->name('index');

    // Détails d'une catégorie spécifique
    // Méthode: GET, URL: /service_categories/{id}, Contrôleur: ServiceCategoryController@show, Nom: service_categories.show
    // {id} est un paramètre dynamique qui sera passé au contrôleur
    Route::get('/{id}', [ServiceCategoryController::class, 'show'])->name('show');
});

// Routes pour les prestataires (accès public)
// Préfixe: /providers
// Groupe de routes avec préfixe et nom de route commun
Route::prefix('providers')->name('providers.')->group(function () {
    // Recherche de prestataires
    // Méthode: GET, URL: /providers/search, Contrôleur: UserController@search, Nom: providers.search
    Route::get('/search', [UserController::class, 'search'])->name('search');

    // Profil public d'un prestataire
    // Méthode: GET, URL: /providers/{user}, Contrôleur: UserController@show, Nom: providers.show
    // {user} est un paramètre dynamique qui sera passé au contrôleur (modèle User)
    Route::get('/{user}', [UserController::class, 'show'])->name('show');

    // Route pour contacter un prestataire
    // Méthode: POST, URL: /providers/{user}/contact, Contrôleur: UserController@contact, Nom: providers.contact
    Route::post('/{user}/contact', [UserController::class, 'contact'])->name('contact');
});

// Pages statiques
// Page "À propos"
// Méthode: GET, URL: /about, Contrôleur: PageController@about, Nom: about
Route::get('/about', [PageController::class, 'about'])->name('about');

// Page de contact (formulaire)
// Méthode: GET, URL: /contact, Contrôleur: PageController@contact, Nom: contact
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

// Envoi du formulaire de contact
// Méthode: POST, URL: /contact, Contrôleur: PageController@sendContact, Nom: contact.send
Route::post('/contact', [PageController::class, 'sendContact'])->name('contact.send');

// Route pour changer la langue de l'application
// Vérifie que la locale est valide (fr ou en) puis la stocke en session
// Méthode: GET, URL: /set-locale/{locale}, Fonction anonyme, Nom: set.locale
// {locale} est un paramètre dynamique qui sera validé dans la fonction
Route::get('/set-locale/{locale}', function ($locale) {
    if (!in_array($locale, ['fr', 'en'])) {
        abort(400); // Retourne une erreur 400 si la locale n'est pas valide
    }
    session(['locale' => $locale]); // Stocke la locale en session
    return back(); // Retourne à la page précédente
})->name('set.locale');


// Route pour le marketplace
Route::get('/marketplace', [PageController::class, 'marketplace'])->name('marketplace');

// Route pour les forums
Route::get('/forums', [PageController::class, 'forums'])->name('forums');

// Route pour les services
Route::get('/services', [PageController::class, 'services'])->name('services');

// Route pour la recherche
Route::get('/search', [PageController::class, 'search'])->name('search');

// =============================================================================
// AUTHENTICATION ROUTES (accessibles uniquement aux invités)
// =============================================================================
// Groupe de routes accessibles uniquement aux utilisateurs non connectés
// Middleware: guest (redirige les utilisateurs connectés)
Route::middleware('guest')->group(function () {
    // Affichage du formulaire de connexion
    // Méthode: GET, URL: /login, Contrôleur: LoginController@showLoginForm, Nom: login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

    // Actions d'authentification
    // Soumission du formulaire de connexion
    // Méthode: POST, URL: /login, Contrôleur: LoginController@login, Nom: login.submit
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

    // Connexion personnalisée (peut-être pour un formulaire spécifique)
    // Méthode: POST, URL: /login/custom, Contrôleur: LoginController@customLogin, Nom: custom.login
    Route::post('/login/custom', [LoginController::class, 'customLogin'])->name('custom.login');

    // Enregistrement d'un nouvel utilisateur
    // Méthode: POST, URL: /register, Contrôleur: RegisterController@register, Nom: register
    Route::post('/register', [RegisterController::class, 'register'])->name('register');

    // Authentification Google
    // Préfixe: /auth/google
    // Groupe de routes avec préfixe et nom de route commun
    Route::prefix('auth/google')->name('google.')->group(function () {
        // Redirection vers Google pour l'authentification
        // Méthode: GET, URL: /auth/google, Contrôleur: GoogleAuthController@redirectToGoogle, Nom: google.login
        Route::get('/', [GoogleAuthController::class, 'redirectToGoogle'])->name('login');

        // Callback après authentification Google
        // Méthode: GET, URL: /auth/google/callback, Contrôleur: GoogleAuthController@handleGoogleCallback, Nom: google.callback
        Route::get('/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('callback');
    });

    // Réinitialisation de mot de passe
    // Envoi du lien de réinitialisation
    // Méthode: POST, URL: /password/forgot, Contrôleur: ForgotPasswordController@sendResetLink, Nom: password.forgot
    Route::post('/password/forgot', [ForgotPasswordController::class, 'sendResetLink'])->name('password.forgot');

    // Affichage du formulaire de réinitialisation
    // Méthode: GET, URL: /password/reset/{token}, Contrôleur: ForgotPasswordController@showResetForm, Nom: password.reset
    // {token} est un paramètre dynamique utilisé pour la réinitialisation
    Route::get('/password/reset/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');

    // Soumission du formulaire de réinitialisation
    // Méthode: POST, URL: /password/reset, Contrôleur: ForgotPasswordController@reset, Nom: password.update
    Route::post('/password/reset', [ForgotPasswordController::class, 'reset'])->name('password.update');
});

// =============================================================================
// EMAIL CONFIRMATION ROUTE (accessible à tous)
// =============================================================================
// Confirmation d'email via un token
// Méthode: GET, URL: /confirm-email/{token}, Contrôleur: RegisterController@confirmEmail, Nom: confirm.email
// {token} est un paramètre dynamique utilisé pour la confirmation
Route::get('/confirm-email/{token}', [RegisterController::class, 'confirmEmail'])->name('confirm.email');

// Affichage du formulaire pour compléter l'inscription
// Méthode: GET, URL: /complete-registration, Contrôleur: RegisterController@showCompleteRegistrationForm, Nom: complete.registration
Route::get('/complete-registration', [RegisterController::class, 'showCompleteRegistrationForm'])->name('complete.registration');

// Soumission du formulaire pour compléter l'inscription
// Méthode: POST, URL: /complete-registration, Contrôleur: RegisterController@completeRegistration, Nom: complete.registration
Route::post('/complete-registration', [RegisterController::class, 'completeRegistration'])->name('complete.registration');

// =============================================================================
// AUTHENTICATED USER ROUTES (accessibles uniquement aux utilisateurs connectés)
// =============================================================================
// Groupe de routes accessibles uniquement aux utilisateurs connectés et non bloqués
// Middleware: auth (nécessite une authentification), check.user.lock (vérifie que le compte n'est pas bloqué)
Route::middleware(['auth', 'check.user.lock'])->group(function () {
    // Déconnexion de l'utilisateur
    // Méthode: POST, URL: /logout, Contrôleur: LoginController@logout, Nom: logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Route pour le géocodage manuel d'une adresse utilisateur
    // Méthode: POST, URL: /user/geocode, Contrôleur: UserController@geocodeUserAddress, Nom: user.geocode
    Route::post('/user/geocode', [UserController::class, 'geocodeUserAddress'])->name('user.geocode');

    // Routes pour le changement de mot de passe
    // Affichage du formulaire de changement de mot de passe
    // Méthode: GET, URL: /change-password, Contrôleur: UserProfileController@showChangePasswordForm, Nom: password.change.form
    Route::get('/change-password', [UserProfileController::class, 'showChangePasswordForm'])->name('password.change.form');

    // Soumission du formulaire de changement de mot de passe
    // Méthode: POST, URL: /change-password, Contrôleur: UserProfileController@changePassword, Nom: password.change
    Route::post('/change-password', [UserProfileController::class, 'changePassword'])->name('password.change');

    // Routes pour les commentaires
    // Enregistrement d'un commentaire (déjà défini plus haut mais redéfini ici avec le même middleware)
    // Méthode: POST, URL: /commentaires, Contrôleur: CommentaireController@store, Nom: commentaires.store
    Route::post('/commentaires', [CommentaireController::class, 'store'])->name('commentaires.store');

    // Routes pour les promotions
    // Affichage du formulaire de création de promotion
    // Méthode: GET, URL: /promotions/create, Contrôleur: PromotionController@create, Nom: promotions.create
    Route::get('/promotions/create', [PromotionController::class, 'create'])->name('promotions.create');

    // Enregistrement d'une promotion
    // Méthode: POST, URL: /promotions, Contrôleur: PromotionController@store, Nom: promotions.store
    Route::post('/promotions', [PromotionController::class, 'store'])->name('promotions.store');

    // Suppression d'une promotion
    // Méthode: DELETE, URL: /promotions/{promotion}, Contrôleur: PromotionController@destroy, Nom: promotions.destroy
    // {promotion} est un paramètre dynamique qui sera passé au contrôleur (modèle Promotion)
    Route::delete('/promotions/{promotion}', [PromotionController::class, 'destroy'])->name('promotions.destroy');

    // Gestion du profil (accessible à tous les utilisateurs connectés)
    // Préfixe: /user
    // Groupe de routes avec préfixe et nom de route commun
    Route::prefix('user')->name('user.')->group(function () {
        // Affichage du profil utilisateur
        // Méthode: GET, URL: /user/profile, Contrôleur: UserProfileController@showProfile, Nom: user.profile
        Route::get('/profile', [UserProfileController::class, 'showProfile'])->name('profile');

        // Mise à jour du profil utilisateur
        // Méthode: POST, URL: /user/profile/update, Contrôleur: UserProfileController@updateProfile, Nom: user.profile.update
        Route::post('/profile/update', [UserProfileController::class, 'updateProfile'])->name('profile.update');

        // Upload d'un avatar
        // Méthode: POST, URL: /user/profile/upload-avatar, Contrôleur: UserProfileController@uploadAvatar, Nom: user.profile.upload_avatar
        Route::post('/profile/upload-avatar', [UserProfileController::class, 'uploadAvatar'])->name('profile.upload_avatar');

        // Upload de photos
        // Méthode: POST, URL: /user/profile/upload-photos, Contrôleur: UserProfileController@uploadPhotos, Nom: user.profile.upload_photos
        Route::post('/profile/upload-photos', [UserProfileController::class, 'uploadPhotos'])->name('profile.upload_photos');

        // Suppression d'une photo
        // Méthode: DELETE, URL: /user/profile/delete-photo/{photoIndex}, Contrôleur: UserProfileController@deletePhoto, Nom: user.profile.delete_photo
        // {photoIndex} est un paramètre dynamique qui sera passé au contrôleur
        Route::delete('/profile/delete-photo/{photoIndex}', [UserProfileController::class, 'deletePhoto'])->name('profile.delete_photo');

        // Récupération des catégories de services
        // Méthode: GET, URL: /user/profile/get-service-categories, Contrôleur: UserProfileController@getServiceCategories, Nom: user.profile.get_service_categories
        Route::get('/profile/get-service-categories', [UserProfileController::class, 'getServiceCategories'])->name('profile.get_service_categories');

        // Récupération des IDs des catégories de services de l'utilisateur
        // Méthode: GET, URL: /user/profile/get-user-service-categories, Contrôleur: UserProfileController@getUserServiceCategoryIds, Nom: user.profile.get_user_service_categories
        Route::get('/profile/get-user-service-categories', [UserProfileController::class, 'getUserServiceCategoryIds'])->name('profile.get_user_service_categories');

        // Mise à jour des catégories de services de l'utilisateur
        // Méthode: POST, URL: /user/profile/update-service-categories, Contrôleur: UserProfileController@updateServiceCategories, Nom: user.profile.update_service_categories
        Route::post('/profile/update-service-categories', [UserProfileController::class, 'updateServiceCategories'])->name('profile.update_service_categories');
    });
});

// =========================================================================
// ADMIN ROUTES (accessibles uniquement aux ADMIN)
// =========================================================================
// Groupe de routes accessibles uniquement aux administrateurs
// Middleware: admin (vérifie que l'utilisateur a le rôle ADMIN)
// Préfixe: /admin
// Nom de route: admin.
// Groupe de routes avec préfixe, nom de route commun et middleware
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    // Gestion des sliders (CRUD complet)
    // Utilisation de resource pour générer automatiquement les routes CRUD
    // Méthode: resource, URL: /admin/sliders, Contrôleur: \App\Http\Controllers\Admin\SliderController
    // Génère automatiquement les routes pour index, create, store, show, edit, update, destroy
    Route::resource('sliders', \App\Http\Controllers\Admin\SliderController::class);

    // Route pour mettre à jour l'ordre des sliders
    // Méthode: POST, URL: /admin/sliders/update-order, Contrôleur: \App\Http\Controllers\Admin\SliderController@updateOrder, Nom: admin.sliders.updateOrder
    Route::post('/sliders/update-order', [\App\Http\Controllers\Admin\SliderController::class, 'updateOrder'])
        ->name('sliders.updateOrder');

    // Route pour vérifier si une position est disponible
    // Méthode: POST, URL: /admin/sliders/check-position, Contrôleur: \App\Http\Controllers\Admin\SliderController@checkPosition, Nom: admin.sliders.checkPosition
    // Middleware supplémentaire: admin (redondant car déjà appliqué au groupe)
    Route::post('/admin/sliders/check-position', [\App\Http\Controllers\Admin\SliderController::class, 'checkPosition'])
        ->name('admin.sliders.checkPosition')
        ->middleware('admin');

    // Route pour corriger les positions des sliders
    // Méthode: GET, URL: /admin/sliders/fix-positions, Contrôleur: \App\Http\Controllers\Admin\SliderController@fixPositions, Nom: admin.sliders.fixPositions
    Route::get('/sliders/fix-positions', [\App\Http\Controllers\Admin\SliderController::class, 'fixPositions'])
        ->name('sliders.fixPositions');

    // Routes principales pour les sliders (CRUD complet)
    Route::resource('sliders', \App\Http\Controllers\Admin\SliderController::class)->except([
        'show' // On exclut show car on a une route personnalisée
    ]);

    // Routes supplémentaires spécifiques aux sliders
    Route::prefix('sliders')->name('sliders.')->group(function () {
        // Route pour mettre à jour l'ordre des sliders
        Route::post('/update-order', [\App\Http\Controllers\Admin\SliderController::class, 'updateOrder'])
            ->name('updateOrder');

        // Route pour vérifier si une position est disponible
        Route::post('/check-position', [\App\Http\Controllers\Admin\SliderController::class, 'checkPosition'])
            ->name('checkPosition');

        // Route pour corriger les positions des sliders
        Route::get('/fix-positions', [\App\Http\Controllers\Admin\SliderController::class, 'fixPositions'])
            ->name('fixPositions');

        // Routes pour les images des sliders (groupées par slider)
        Route::prefix('/{slider}')->group(function () {
            // Édition des images d'un slider
            Route::get('/images', [\App\Http\Controllers\Admin\SliderImageController::class, 'editImages'])
                ->name('images.edit');

            // Ajout d'une image à un slider
            Route::post('/images', [\App\Http\Controllers\Admin\SliderImageController::class, 'storeImage'])
                ->name('images.store');

            // Mise à jour d'une image
            Route::put('/images/{image}', [\App\Http\Controllers\Admin\SliderImageController::class, 'updateImage'])
                ->name('images.update');

            // Suppression d'une image
            Route::delete('/images/{image}', [\App\Http\Controllers\Admin\SliderImageController::class, 'destroyImage'])
                ->name('images.destroy');

            // Mise à jour de l'ordre des images
            Route::post('/images/update-order', [\App\Http\Controllers\Admin\SliderImageController::class, 'updateOrder'])
                ->name('images.updateOrder');
        });
    });

    // Tableau de bord administrateur
    // Méthode: GET, URL: /admin/dashboard, Fonction anonyme, Nom: admin.dashboard
    // Retourne simplement la vue du tableau de bord
    Route::get('/dashboard', function () { return view('admin.dashboard'); })->name('dashboard');

    // Gestion des catégories de services (CRUD complet)
    // Préfixe: /admin/service_categories
    // Groupe de routes avec préfixe et nom de route commun
    Route::prefix('service_categories')->name('service_categories.')->group(function () {
        // Liste des catégories de services (version admin)
        // Méthode: GET, URL: /admin/service_categories/, Contrôleur: ServiceCategoryController@adminIndex, Nom: admin.service_categories.index
        Route::get('/', [ServiceCategoryController::class, 'adminIndex'])->name('index');

        // Affichage du formulaire de création
        // Méthode: GET, URL: /admin/service_categories/create, Contrôleur: ServiceCategoryController@create, Nom: admin.service_categories.create
        Route::get('/create', [ServiceCategoryController::class, 'create'])->name('create');

        // Enregistrement d'une nouvelle catégorie
        // Méthode: POST, URL: /admin/service_categories/, Contrôleur: ServiceCategoryController@store, Nom: admin.service_categories.store
        Route::post('/', [ServiceCategoryController::class, 'store'])->name('store');

        // Affichage du formulaire d'édition
        // Méthode: GET, URL: /admin/service_categories/{id}/edit, Contrôleur: ServiceCategoryController@edit, Nom: admin.service_categories.edit
        Route::get('/{id}/edit', [ServiceCategoryController::class, 'edit'])->name('edit');

        // Mise à jour d'une catégorie
        // Méthode: PUT, URL: /admin/service_categories/{id}, Contrôleur: ServiceCategoryController@update, Nom: admin.service_categories.update
        Route::put('/{id}', [ServiceCategoryController::class, 'update'])->name('update');

        // Suppression d'une catégorie
        // Méthode: DELETE, URL: /admin/service_categories/{id}, Contrôleur: ServiceCategoryController@destroy, Nom: admin.service_categories.destroy
        Route::delete('/{id}', [ServiceCategoryController::class, 'destroy'])->name('destroy');

        // Validation d'une catégorie
        // Méthode: POST, URL: /admin/service_categories/{id}/validate, Contrôleur: ServiceCategoryController@validateCategory, Nom: admin.service_categories.validate
        Route::post('/{id}/validate', [ServiceCategoryController::class, 'validateCategory'])->name('validate');

        // Définition d'une catégorie comme "du mois"
        // Méthode: POST, URL: /admin/service_categories/{id}/set-as-monthly, Contrôleur: ServiceCategoryController@setAsMonthly, Nom: admin.service_categories.set_as_monthly
        Route::post('/{id}/set-as-monthly', [ServiceCategoryController::class, 'setAsMonthly'])->name('set_as_monthly');

        // Transfert des prestataires d'une catégorie à une autre
        // Méthode: POST, URL: /admin/service_categories/{id}/transfer-providers, Contrôleur: ServiceCategoryController@transferProviders, Nom: admin.service_categories.transfer_providers
        Route::post('/{id}/transfer-providers', [ServiceCategoryController::class, 'transferProviders'])->name('transfer_providers');
    });
});





Route::middleware(['auth'])->group(function () {
    Route::get('/forums/{forum}/topics/create', [ForumTopicController::class, 'create'])->name('forums.topics.create');
    Route::post('/forums/{forum}/topics', [ForumTopicController::class, 'store'])->name('forums.topics.store');
});

// Sujets et réponses
Route::post('/forums/{forum}/topics/{topic}/replies', [ForumTopicController::class, 'storeReply'])
    ->name('forums.topics.replies.store')
    ->middleware('auth');
Route::get('/forums/{forum}/topics/{topic}', [ForumTopicController::class, 'show'])->name('forums.topics.show');


// Forums
Route::resource('forums', ForumController::class);
Route::middleware(['auth'])->group(function () {
    Route::get('/forums/create', [ForumController::class, 'create'])->name('forums.create');
    Route::post('/forums', [ForumController::class, 'store'])->name('forums.store');
});

Route::get('/forums/search', [\App\Http\Controllers\Admin\ForumController::class, 'search'])->name('forums.search');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Routes pour gérer les forums
    Route::get('/forums/create', [\App\Http\Controllers\Admin\ForumController::class, 'create'])->name('forums.create');
    Route::get('/forums/{forum}/edit', [\App\Http\Controllers\Admin\ForumController::class, 'edit'])->name('forums.edit');
    Route::put('/forums/{forum}', [\App\Http\Controllers\Admin\ForumController::class, 'update'])->name('forums.update');
    Route::delete('/forums/{forum}', [\App\Http\Controllers\Admin\ForumController::class, 'destroy'])->name('forums.destroy');
    Route::get('/forums', [\App\Http\Controllers\Admin\ForumController::class, 'index'])->name('forums.index');
    Route::post('/forums', [\App\Http\Controllers\Admin\ForumController::class, 'store'])->name('forums.store');
});


// Likes sur les réponses
Route::post('/forum-replies/{reply}/like', [ForumLikeController::class, 'toggleLike'])
    ->name('forum.replies.like')
    ->middleware('auth');





Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Routes pour gérer les catégories de forums
    Route::get('/forum_categories', [ForumCategoryController::class, 'index'])->name('forum_categories.index');
    Route::get('/forum_categories/create', [ForumCategoryController::class, 'create'])->name('forum_categories.create');
    Route::post('/forum_categories', [ForumCategoryController::class, 'store'])->name('forum_categories.store');
    Route::get('/forum_categories/{forumCategory}/edit', [ForumCategoryController::class, 'edit'])->name('forum_categories.edit');
    Route::put('/forum_categories/{forumCategory}', [ForumCategoryController::class, 'update'])->name('forum_categories.update');
    Route::delete('/forum_categories/{forumCategory}', [ForumCategoryController::class, 'destroy'])->name('forum_categories.destroy');
});




// Tableau de bord utilisateur
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard')->middleware('auth');
