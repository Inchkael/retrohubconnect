<?php
namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Favorite;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with(['user', 'category', 'images', 'reviews'])
            ->where('is_active', true)
            ->latest()
            ->get();
        return view('marketplace.items.index', compact('items'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('marketplace.items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'condition' => 'required|in:new,used,collector',
            'location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:5120',
        ]);

        try {
            // Création de l'article
            $item = Item::create([
                'user_id' => Auth::id(),
                'category_id' => $validated['category_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'condition' => $validated['condition'],
                'location' => $validated['location'] ?? null,
                'is_active' => $request->has('is_active'),
                'is_sold' => false,
            ]);

            // Gestion des images
            if ($request->hasFile('images')) {
                $position = 1;
                foreach ($request->file('images') as $image) {
                    $path = $image->store('items', 'public');

                    $item->images()->create([
                        'path' => $path,
                        'original_name' => $image->getClientOriginalName(),
                        'mime_type' => $image->getMimeType(),
                        'size' => $image->getSize(),
                        'format' => $image->getClientOriginalExtension(),
                        'type' => 'original',
                        'position' => $position++,
                    ]);
                }
            }

            return redirect()->route('marketplace.items.show', $item)
                ->with('success', 'Article créé avec succès !');

        } catch (\Exception $e) {
            \Log::error("Erreur création article: " . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function show(Item $item)
    {
        $item->load(['user', 'category', 'images', 'favorites', 'reviews']);
        return view('marketplace.items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        if (Auth::id() !== $item->user_id) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cet article.');
        }

        $categories = Category::all();
        return view('marketplace.items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        if (Auth::id() !== $item->user_id) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cet article.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'condition' => 'required|in:new,used,collector',
            'location' => 'nullable|string|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:5120',
        ]);

        $item->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'category_id' => $validated['category_id'],
            'condition' => $validated['condition'],
            'location' => $validated['location'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        // Gestion des nouvelles images
        if ($request->hasFile('images')) {
            $position = $item->images()->max('position') + 1;
            foreach ($request->file('images') as $image) {
                $path = $image->store('items', 'public');
                $item->images()->create([
                    'path' => $path,
                    'original_name' => $image->getClientOriginalName(),
                    'mime_type' => $image->getMimeType(),
                    'size' => $image->getSize(),
                    'format' => $image->getClientOriginalExtension(),
                    'type' => 'original',
                    'position' => $position++,
                ]);
            }
        }

        return redirect()->route('marketplace.items.show', $item)
            ->with('success', 'Article mis à jour avec succès !');
    }

    public function destroy(Item $item)
    {
        if (Auth::id() !== $item->user_id) {
            abort(403, 'Vous n\'êtes pas autorisé à supprimer cet article.');
        }

        // Supprimer les images associées
        foreach ($item->images as $image) {
            $image->delete(); // L'observateur se charge de supprimer le fichier
        }

        $item->delete();
        return redirect()->route('marketplace.items.index')
            ->with('success', 'Article supprimé avec succès !');
    }

    public function toggleFavorite(Item $item)
    {
        $favorite = Favorite::where('user_id', Auth::id())
            ->where('item_id', $item->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return back()->with('success', 'Article retiré des favoris.');
        } else {
            Favorite::create([
                'user_id' => Auth::id(),
                'item_id' => $item->id,
            ]);
            return back()->with('success', 'Article ajouté aux favoris.');
        }
    }

    public function destroyImage(Image $image)
    {
        if (Auth::id() !== $image->imageable->user_id) {
            abort(403, 'Vous n\'êtes pas autorisé à supprimer cette image.');
        }

        $image->delete(); // L'observateur se charge de supprimer le fichier
        return back()->with('success', 'Image supprimée avec succès !');
    }
}
