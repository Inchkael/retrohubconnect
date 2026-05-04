<?php

namespace App\Http\Controllers;

use App\Models\ForumReply;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Slider;
use App\Models\Item;
use App\Models\Forum;

/**
 * Contrôleur HomeController
 *
 * Ce contrôleur gère les fonctionnalités principales de la page d'accueil,
 * notamment l'affichage des prestataires et la recherche parmi eux.
 * Il fournit les méthodes nécessaires pour afficher la page d'accueil avec les prestataires
 * et pour effectuer des recherches parmi les prestataires en fonction de différents critères.
 */
class HomeController extends Controller
{

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    /**
     * Display the home page with all providers by default
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get all providers (users with PROVIDER role) with their service categories
        $providers = User::where('role', 'PROVIDER')
            ->with('serviceCategories')
            ->paginate(9); // 9 results per page

        // Get all service categories for the filter
        $categories = \App\Models\ServiceCategory::where('is_validated', true)->get();

        $monthService = \App\Models\ServiceCategory::where('is_monthly', true)->first();

        // Récupérer les 4 derniers partenaires (nouveau)
        $recentProviders = User::where('role', 'PROVIDER')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->with('serviceCategories')
            ->get();

        $recentForums = Forum::with('topics')->latest()->take(3)->get();
        $recentUsers = User::latest()->take(4)->get();   // Derniers 4 utilisateurs
        $recentItems = Item::latest()->take(4)->get();    // Derniers 4 items

        $lastItems = Item::latest()->take(3)->get();

        $featuredItem = Item::where('is_featured', true)->latest()->first();

        $sliders = Slider::getActiveOrdered();

        return view('home', compact('featuredItem','lastItems','providers', 'categories','monthService','recentProviders','sliders','recentForums','recentUsers','recentItems'));
    }

    /**
     * Search for providers based on user input
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $category = $request->input('category');

        $providers = User::where('role', 'PROVIDER')
            ->when($query, function($q) use ($query) {
                return $q->where(function($q) use ($query) {
                    $q->where('first_name', 'like', "%{$query}%")
                        ->orWhere('last_name', 'like', "%{$query}%")
                        ->orWhere('address', 'like', "%{$query}%")
                        ->orWhere('mobile_phone', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%")
                        ->orWhereHas('serviceCategories', function($q) use ($query) {
                            $q->where('name', 'like', "%{$query}%");
                        });
                });
            })
            ->when($category, function($q) use ($category) {
                return $q->whereHas('serviceCategories', function($q) use ($category) {
                    $q->where('id', $category);
                });
            })
            ->with('serviceCategories')
            ->paginate(9);

        $categories = \App\Models\ServiceCategory::where('is_validated', true)->get();

        // Récupérer les 4 derniers partenaires (nouveau)
        $recentProviders = User::where('role', 'PROVIDER')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->with('serviceCategories')
            ->get();

        return view('home', compact('providers', 'categories', 'recentProviders'));
    }


}
