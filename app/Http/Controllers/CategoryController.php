<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('marketplace.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('marketplace.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories',
            'description' => 'nullable|string',
        ]);

        Category::create($validated);

        return redirect()->route('marketplace.categories.index')
            ->with('success', 'Catégorie créée avec succès !');
    }

    public function show(Category $category)
    {
        return view('marketplace.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('marketplace.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()->route('marketplace.categories.show', $category)
            ->with('success', 'Catégorie mise à jour avec succès !');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('marketplace.categories.index')
            ->with('success', 'Catégorie supprimée avec succès !');
    }
}
