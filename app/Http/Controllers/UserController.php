<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\GeocodingService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Notifications\ContactPrestataireNotification;
use App\Models\ServiceCategory;


/**
 * UserController
 *
 * Ce contrôleur gère les opérations CRUD (Create, Read, Update, Delete) pour les utilisateurs,
 * avec un focus particulier sur les prestataires (utilisateurs avec rôle 'PROVIDER').
 * Il inclut également des fonctionnalités spécifiques comme la recherche de prestataires,
 * le géocodage des adresses et la gestion des contacts avec les prestataires.
 */
class UserController extends Controller
{
    protected $geocodingService;

    public function __construct(GeocodingService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }
    /**
     * Affiche la liste des utilisateurs.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Filtre pour n'afficher que les prestataires (PROVIDER)
        $providers = User::where('role', 'PROVIDER')->paginate(9); // Pagination avec 9 résultats par page;
        return view('users.index', compact('providers'));
    }

    /**
     * Affiche le profil d'un prestataire (utilisateur avec rôle PROVIDER).
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    /**
     * Affiche le profil d'un prestataire
     */
    public function show(User $user)
    {
        $user->load(['promotions.categorieDeServices', 'serviceCategories']);
        // Vérification du rôle
        if ($user->role !== 'PROVIDER') {
            abort(404);
        }

        // Debug: Afficher les données de l'utilisateur
        \Log::info("Affichage du prestataire {$user->id}:", [
            'address' => $user->address,
            'latitude' => $user->latitude,
            'longitude' => $user->longitude,
            'getFullName' => $user->getFullName()
        ]);

        // Si l'adresse existe mais pas les coordonnées, essayer de géocoder
        if ($user->address && (!$user->latitude || !$user->longitude)) {
            \Log::info("Tentative de géocodage pour {$user->address}");

            try {
                $result = $this->geocodingService->geocodeAddress($user->address);

                if ($result) {
                    $user->update([
                        'latitude' => $result['latitude'],
                        'longitude' => $result['longitude']
                    ]);
                    $user->refresh(); // Recharger les données
                    \Log::info("Géocodage réussi: lat={$result['latitude']}, lon={$result['longitude']}");
                } else {
                    \Log::warning("Échec du géocodage pour l'adresse: {$user->address}");
                }
            } catch (\Exception $e) {
                \Log::error("Erreur lors du géocodage: " . $e->getMessage());
            }
        }

        return view('providers.show', compact('user'));
    }

    /**
     * Affiche le formulaire de création d'un utilisateur.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Enregistre un nouvel utilisateur.
     */
    public function store(Request $request)
    {
        // Validation et création d'un nouvel utilisateur
        $validated = $request->validate([
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:ADMIN,USER,PROVIDER,TEMP',
            'address' => 'nullable|string|max:255',
            'mobile_phone' => 'nullable|string|max:20',
            'website' => 'nullable|url',
        ]);

        // Créer l'utilisateur
        $user = User::create($validated);

        // Géocoder l'adresse si elle est fournie
        if ($user->address) {
            $this->geocodingService->geocodeUserAddress($user);
        }

        return redirect()->route('users.show', $user)
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Affiche le formulaire d'édition d'un utilisateur.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Met à jour un utilisateur.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'address' => 'nullable|string|max:255',
            'mobile_phone' => 'nullable|string|max:20',
            'website' => 'nullable|url',
        ]);

        // Mettre à jour les données de base
        $user->update($validated);

        // Géocoder uniquement si l'adresse a changé ou si les coordonnées sont manquantes
        if ($request->has('address') && ($user->wasChanged('address') || !$user->latitude || !$user->longitude)) {
            $this->geocodingService->geocodeUserAddress($user);
        }

        return redirect()->route('users.show', $user)->with('success', 'Profil mis à jour.');
    }


    /**
     * Supprime un utilisateur.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Recherche des prestataires selon des critères (nom, localité, code postal, commune, service).
     * Utilise des requêtes "LIKE" avec des jokers (%) pour trouver des correspondances partielles.
     *
     * @param  \Illuminate\Http\Request  $request  Objet Request de Laravel contenant les données de la requête HTTP
     * @return \Illuminate\View\View               Retourne une vue Laravel avec les résultats de la recherche
     */
    public function search(Request $request)
    {
        // =============================================================================
        // 1. RÉCUPÉRATION DU TERME DE RECHERCHE
        // =============================================================================
        // Récupère la valeur du paramètre 'query' depuis la requête HTTP
        // Exemple: si l'utilisateur recherche "massage Liége", $query contiendra cette chaîne
        $query = $request->input('query');

        // =============================================================================
        // 2. SÉPARATION DES TERMES DE RECHERCHE
        // =============================================================================
        // Découpe la chaîne de recherche en un tableau de termes individuels
        // Basé sur les espaces comme séparateurs
        // Exemple: "massage Liége" devient ["massage", "Liége"]
        // Cette technique est utile pour une recherche multi-critères
        $terms = explode(' ', $query);

        // =============================================================================
        // 3. CONSTRUCTION DE LA REQUÊTE ELOQUENT
        // =============================================================================
        // Construit une requête pour les utilisateurs ayant le rôle 'PROVIDER' (prestataires)
        // Utilise la méthode when() pour appliquer les filtres uniquement si un terme de recherche est fourni
        $providers = User::where('role', 'PROVIDER')

            // =========================================================================
            // 4. APPLICATION DES FILTRES DE RECHERCHE (quand un terme est fourni)
            // =========================================================================
            ->when($query, function($q) use ($terms) {
                // Pour chaque terme de recherche trouvé
                foreach ($terms as $term) {
                    // Ajoute une condition WHERE imbriquée pour ce terme spécifique
                    // Cela crée une requête du type: WHERE (condition1 OR condition2 OR...) AND (condition1 OR condition2 OR...) pour chaque terme
                    $q->where(function($q) use ($term) {
                        // Recherche dans le nom de famille avec des jokers (%) pour des correspondances partielles
                        // Exemple: "%massage%" trouvera "massage", "massages", "massage relaxant", etc.
                        $q->where('last_name', 'like', "%{$term}%")

                            // Recherche dans le prénom avec les mêmes jokers
                            ->orWhere('first_name', 'like', "%{$term}%")

                            // Recherche dans l'adresse (peut inclure la localité, code postal, rue, etc.)
                            ->orWhere('address', 'like', "%{$term}%")

                            // Recherche dans les catégories de services associées au prestataire
                            // Utilise une sous-requête pour vérifier les noms de catégories
                            // Exemple: si le terme est "yoga", trouvera les prestataires proposant des services de yoga
                            ->orWhereHas('serviceCategories', function($q) use ($term) {
                                $q->where('name', 'like', "%{$term}%");
                            });
                    });
                }
            })

            // =========================================================================
            // 5. PAGINATION DES RÉSULTATS
            // =========================================================================
            // Applique une pagination aux résultats pour une meilleure expérience utilisateur
            // 9 résultats par page
            // La pagination permet de:
            // - Limiter la charge sur la base de données
            // - Améliorer les performances de chargement
            // - Offrir une navigation plus intuitive pour l'utilisateur
            ->paginate(9);


        // Récupération du service du mois
        $monthService = \App\Models\ServiceCategory::where('is_monthly', true)->first();

        // Récupération des 4 derniers partenaires
        $recentProviders = User::where('role', 'PROVIDER')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->with('serviceCategories')
            ->get();

        // =============================================================================
        // 6. RETOUR DE LA VUE AVEC LES RÉSULTATS
        // =============================================================================
        // Retourne la vue 'home' en passant les prestataires trouvés
        // La méthode compact() crée un tableau associatif avec les variables locales
        // Ici, elle transmet la variable $providers à la vue
        return view('home', compact('providers','monthService', 'recentProviders'));
    }

    public function providerDashboard()
    {
        // Logique pour afficher le tableau de bord du prestataire
        return view('provider.dashboard');
    }


    /**
     * Géocode manuellement l'adresse de l'utilisateur connecté
     */
    public function geocodeUserAddress(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:255'
        ]);

        $user = $request->user();

        try {
            $result = $this->geocodingService->geocodeAddress($request->address);

            if ($result) {
                $user->update([
                    'address' => $request->address,
                    'latitude' => $result['latitude'],
                    'longitude' => $result['longitude']
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Adresse géocodée avec succès',
                    'coordinates' => [
                        'latitude' => $result['latitude'],
                        'longitude' => $result['longitude']
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Aucun résultat trouvé pour cette adresse'
            ], 400);

        } catch (\Exception $e) {
            Log::error("Erreur de géocodage pour l'utilisateur {$user->id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du géocodage: ' . $e->getMessage()
            ], 500);
        }
    }

    public function contact(Request $request, User $user)
    {
        $validated = $request->validate([
            'nom' => 'nullable|string|max:255',
            'email' => 'required|email',
            'objet' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            $user->notify(new ContactPrestataireNotification(
                $validated['nom'],
                $validated['email'],
                $validated['objet'],
                $validated['message'],
                route('providers.show', $user->id)
            ));

            return back()->with('success', 'Votre message a été envoyé avec succès !');
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de l\'envoi du message.');
        }
    }




}
