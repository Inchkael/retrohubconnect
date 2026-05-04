<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Forum;
use App\Models\ForumTopic;
use App\Models\ForumReply;
use Illuminate\Support\Facades\Auth;
use Parsedown;

class ForumTopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        return view('forums.topics.create', compact('forum'));
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
        $topic->update(['locked' => true]);
        return back()->with('success', 'Sujet verrouillé avec succès !');
    }

    public function unlock(ForumTopic $topic)
    {
        $topic->update(['locked' => false]);
        return back()->with('success', 'Sujet déverrouillé avec succès !');
    }

    public function destroy(ForumTopic $topic)
    {
        $topic->delete();
        return back()->with('success', 'Sujet supprimé avec succès !');
    }
}
