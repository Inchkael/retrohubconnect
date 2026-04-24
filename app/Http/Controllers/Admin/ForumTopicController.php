<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Forum;
use App\Models\ForumTopic;
use App\Models\ForumReply;
use App\Models\ForumCategory;
use Illuminate\Support\Facades\Auth;
use Parsedown;

class ForumTopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request, Forum $forum = null)
    {
        $query = ForumTopic::with(['forum.category', 'user'])->withCount('replies');

        // Si un forum spécifique est passé en paramètre
        if ($forum) {
            $query->where('forum_id', $forum->id);
            $topics = $query->latest()->get();
            return view('admin.forums.topics.index', compact('forum', 'topics'));
        }

        // Si un category_id est passé dans la requête
        if ($request->has('category_id')) {
            $categoryId = $request->category_id;
            $query->whereHas('forum', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });

            $category = ForumCategory::find($categoryId);
            $topics = $query->latest()->get();
            return view('admin.forums.topics.index', [
                'topics' => $topics,
                'category' => $category
            ]);
        }

        // Sinon, on affiche tous les sujets
        $topics = $query->latest()->get();
        return view('admin.forums.topics.index', compact('topics'));
    }


    /**
     * Display the specified resource.
     */
    public function show(Forum $forum, ForumTopic $topic)
    {
        $parsedown = new Parsedown();
        $replies = $topic->replies()
            ->with(['user'])
            ->latest()
            ->get();

        return view('forums.topics.show', compact('forum', 'topic', 'replies', 'parsedown'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Forum $forum, ForumTopic $topic)
    {
        // On récupère les catégories pour éventuellement permettre de changer le topic de place
        $categories = ForumCategory::with('forums')->get();

        return view('admin.forums.topics.edit', compact('forum', 'topic', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Forum $forum, ForumTopic $topic)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $topic->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('admin.forums.topics.index', $forum)
            ->with('success', 'Le sujet a été mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */

    /**
     * // Ajouter une réponse à un sujet
     * @param Request $request
     * @param ForumTopic $topic
     * @return \Illuminate\Http\RedirectResponse
     * */

    public function storeReply(Request $request, Forum $forum, ForumTopic $topic)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $reply = ForumReply::create([
            'topic_id' => $topic->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
            'quote_id' => $request->quote_id,
        ]);

        return redirect()->route('forums.topics.show', [$forum, $topic])->with('success', 'Réponse ajoutée avec succès !');
    }


    public function create(Forum $forum)
    {
        return view('admin.forums.topics.create', compact('forum'));
    }

    public function store(Request $request, Forum $forum)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $topic = $forum->topics()->create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('forums.topics.show', [$forum, $topic])
            ->with('success', 'Sujet créé avec succès !');
    }

    public function storeTopic(Request $request, Forum $forum)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $topic = $forum->topics()->create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('forums.topics.show', [$forum, $topic])
            ->with('success', 'Sujet créé avec succès !');
    }

    public function lock(ForumTopic $topic)
    {
        $topic->update(['is_locked' => true]);
        return back()->with('success', 'Sujet verrouillé avec succès !');
    }

    public function unlock(ForumTopic $topic)
    {
        $topic->update(['is_locked' => false]);
        return back()->with('success', 'Sujet déverrouillé avec succès !');
    }

    public function destroy(ForumTopic $topic)
    {
        $topic->delete();
        return back()->with('success', 'Sujet supprimé avec succès !');
    }


    /**
     * Get all topics for global management
     */
public function globalIndex(Request $request)
{
    return $this->index($request);
}

    /**
     * Get topics by category
     */
public function byCategory(Request $request, $category_id)
{
    return $this->index($request->merge(['category_id' => $category_id]));
}


}
