<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Forum;
use App\Models\ForumCategory;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    public function index()
    {
        $forums = Forum::with('category')->get();
        return view('admin.forums.index', compact('forums'));
    }

    public function create()
    {
        $categories = ForumCategory::all();
        return view('admin.forums.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:forum_categories,id',
        ]);

        Forum::create($request->all());

        return redirect()->route('admin.forums.index')->with('success', 'Forum créé avec succès !');
    }

    public function edit(Forum $forum)
    {
        $categories = ForumCategory::all();
        return view('admin.forums.edit', compact('forum', 'categories'));
    }

    public function update(Request $request, Forum $forum)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:forum_categories,id',
        ]);

        $forum->update($request->all());

        return redirect()->route('admin.forums.index')->with('success', 'Forum mis à jour avec succès !');
    }

    public function destroy(Forum $forum)
    {
        $forum->delete();

        return redirect()->route('admin.forums.index')->with('success', 'Forum supprimé avec succès !');
    }
}
