<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use Illuminate\Http\Request;
use App\Models\Forum;
use App\Models\Reply;
use App\Models\Message;
use App\Models\User;
use App\Models\ForumTopic;
use Illuminate\Support\Facades\Auth;
use Parsedown;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $parsedown = new Parsedown();


        // Charger les catégories avec leurs forums + compteurs
        $categories = ForumCategory::with(['forums' => function($query) {
            $query->withCount(['topics', 'replies']); // On demande le compte des deux relations
        }])->get();

        // Charger les sujets récents (déjà correct dans ton code)
        $recentTopics = ForumTopic::with(['user', 'forum'])
            ->withCount('replies')
            ->whereNotNull('created_at')
            ->latest()
            ->limit(5)
            ->get();

        return view('forums.index', compact('categories', 'recentTopics', 'parsedown'));
    }

    /**
     * Show the form for creating a new forum.
     */
    public function create(Request $request)
    {
        $categoryId = $request->query('category');
        $category = ForumCategory::findOrFail($categoryId);
        return view('forums.create', compact('category'));
    }

    /**
     * Store a newly created forum in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:forum_categories,id',
        ]);

        // Ajoute l'ID de l'utilisateur connecté
        $forumData = $request->all();
        $forumData['user_id'] = Auth::id();

        Forum::create($forumData);

        return redirect()->route('forums.index')->with('success', 'Votre proposition de forum a été soumise avec succès !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Forum $forum)
    {
        $parsedown = new Parsedown();

        $topics = $forum->topics()
            ->with(['user'])
            ->withCount('replies')
            ->latest()
            ->get()
            ->map(function ($topic) {
                // Si le sujet n'a pas d'utilisateur ou de date de création, on lui attribue des valeurs par défaut
                if (!$topic->user) {
                    $topic->user = (object) ['name' => 'Utilisateur inconnu'];
                }
                if (!$topic->created_at) {
                    $topic->created_at = now(); // Date actuelle si non définie
                }
                return $topic;
            });

        return view('forums.show', compact('forum', 'topics', 'parsedown'));
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
     * Créer un nouveau sujet dans un forum
     * @param Request $request
     * @param Forum $forum
     * @return \Illuminate\Http\RedirectResponse
     */


    public function search(Request $request)
    {
        $query = $request->input('query');

        $categories = ForumCategory::with(['forums' => function($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->withCount('topics');
        }])->get();

        $recentTopics = ForumTopic::with(['user', 'forum'])
            ->withCount('replies')
            ->where('title', 'like', "%{$query}%")
            ->orWhere('content', 'like', "%{$query}%")
            ->latest()
            ->limit(5)
            ->get();

        $parsedown = new Parsedown();

        return view('forums.index', compact('categories', 'recentTopics', 'parsedown', 'query'));
    }

    /**
     * Signale un commentaire à l'administrateur
     */
    public function reportReply(Request $request, Reply $reply)
    {
        $validated = $request->validate([
            'reason' => 'required|string|in:spam,abusive,off_topic,duplicate,other',
            'comment' => 'nullable|string|max:1000',
            'notify_admin' => 'sometimes|boolean',
        ]);

        // Marquer le commentaire comme signalé
        $reply->update([
            'is_reported' => true,
            'report_reason' => $validated['reason'],
            'report_comment' => $validated['comment'] ?? null,
        ]);

        // Envoyer un message à l'administrateur si demandé
        if ($request->has('notify_admin') && $request->notify_admin) {
            $admin = User::where('role', 'ADMIN')->first();

            if ($admin) {
                $reportingUser = Auth::user();
                $messageContent = "Signalement d'un commentaire dans le forum\n\n" .
                    "Sujet: " . $reply->topic->title . "\n" .
                    "Raison: " . $validated['reason'] . "\n" .
                    "Commentaire du signalement: " . ($validated['comment'] ?? 'Aucun') . "\n\n" .
                    "Commentaire signalé:\n" .
                    $reply->content . "\n\n" .
                    "Par: " . $reply->user->name . "\n" .
                    "Signalé par: " . $reportingUser->name . "\n" .
                    "Lien: " . route('forums.topics.show', [$reply->topic->forum, $reply->topic]);

                Message::create([
                    'sender_id' => $reportingUser->id,
                    'recipient_id' => $admin->id,
                    'subject' => "[Signalement] Commentaire signalé dans: " . $reply->topic->title,
                    'content' => $messageContent,
                    'is_abuse_report' => true,
                    'is_read' => false,
                ]);
            }
        }

        return back()->with('success', 'Le commentaire a été signalé avec succès !');
    }


}
