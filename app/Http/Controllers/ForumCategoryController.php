<?php
// app/Http/Controllers/ForumCategoryController.php
namespace App\Http\Controllers;

use App\Models\ForumCategory;
use Illuminate\Http\Request;

class ForumCategoryController extends Controller
{
    public function index()
    {
        $categories = ForumCategory::all();
        return view('admin.forum_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.forum_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        ForumCategory::create($request->all());

        return redirect()->route('admin.forum_categories.index')->with('success', 'Catégorie créée avec succès !');
    }

    public function edit(ForumCategory $forumCategory)
    {
        return view('admin.forum_categories.edit', compact('forumCategory'));
    }

    public function update(Request $request, ForumCategory $forumCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $forumCategory->update($request->all());

        return redirect()->route('admin.forum_categories.index')->with('success', 'Catégorie mise à jour avec succès !');
    }

    public function destroy(ForumCategory $forumCategory)
    {
        $forumCategory->delete();

        return redirect()->route('admin.forum_categories.index')->with('success', 'Catégorie supprimée avec succès !');
    }
}
