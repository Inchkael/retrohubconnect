<?php
// app/Http/Controllers/ReviewController.php
namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Item;
use App\Models\ForumReply; // Ton modèle spécifique
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    /**
     * Enregistre un nouvel avis pour un article
     */
    public function store(Request $request, Item $item)
    {
        try {
            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string|max:1000',
            ]);

            $existingReview = Review::where('user_id', Auth::id())
                ->where(function($query) use ($item) {
                    $query->where('item_id', $item->id)
                        ->orWhere(function($query) use ($item) {
                            $query->where('reviewable_type', 'App\Models\Item')
                                ->where('reviewable_id', $item->id);
                        });
                })
                ->first();

            if ($existingReview) {
                return back()->with('error', 'Vous avez déjà laissé un avis pour cet article.');
            }

            Review::create([
                'user_id' => Auth::id(),
                'reviewable_type' => 'App\Models\Item',
                'reviewable_id' => $item->id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'is_helpful' => 0,
                'is_reported' => false,
            ]);

            return back()->with('success', 'Votre avis a été enregistré avec succès !');

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'enregistrement de l'avis: " . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Marque un avis comme utile
     */
    public function markHelpful(Review $review)
    {
        try {
            $review->increment('is_helpful');
            return back()->with('success', 'Merci pour votre retour !');
        } catch (\Exception $e) {
            Log::error("Erreur lors du marquage utile: " . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Signale un avis ou un commentaire à TOUS les administrateurs
     */
    public function report(Request $request, $type, $id)
    {
        try {
            $validated = $request->validate([
                'reason' => 'required|string|in:spam,abusive,fake,off_topic,duplicate,other',
                'comment' => 'nullable|string|max:1000',
                'notify_admin' => 'sometimes|boolean',
            ]);

            $modelType = $type === 'item' ? 'App\Models\Item' : 'App\Models\ForumReply';
            $model = $modelType::findOrFail($id);

            // Enregistrement du signalement
            $review = Review::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'reviewable_type' => $modelType,
                    'reviewable_id' => $id,
                ],
                [
                    'rating' => 0,
                    'is_reported' => true,
                    'report_reason' => $validated['reason'],
                    'report_comment' => $validated['comment'] ?? null,
                ]
            );

            // Notification aux Admins
            if ($request->has('notify_admin') && $request->notify_admin) {
                $admins = User::where('role', 'ADMIN')->get();
                $reportingUser = Auth::user();

                if ($admins->isNotEmpty()) {
                    if ($type === 'item') {
                        // Ici on récupère le contenu de l'avis original pour l'admin
                        $messageContent = "SIGNALEMENT D'AVIS SUR ARTICLE\n" .
                            "----------------------------------\n" .
                            "Article : " . ($model->title ?? 'N/A') . "\n" .
                            "Raison : " . $validated['reason'] . "\n" .
                            "Commentaire signalement : " . ($validated['comment'] ?? 'Aucun') . "\n\n" .
                            "Auteur de l'avis : " . ($model->user->name ?? 'Inconnu') . "\n" .
                            "Signalé par : " . $reportingUser->name . "\n" .
                            "Lien : " . route('marketplace.items.show', $model);

                        $subject = "[Signalement] Avis sur " . ($model->title ?? 'Article');
                    } else {
                        $messageContent = "SIGNALEMENT COMMENTAIRE FORUM\n" .
                            "----------------------------------\n" .
                            "Sujet : " . ($model->topic->title ?? 'Inconnu') . "\n" .
                            "Contenu : " . $model->content . "\n" .
                            "Signalé par : " . $reportingUser->name . "\n" .
                            "Lien : " . ($model->topic ? route('forums.topics.show', [$model->topic->forum, $model->topic]) : '#');

                        $subject = "[Signalement] Forum : " . ($model->topic->title ?? 'Commentaire');
                    }

                    foreach ($admins as $admin) {
                        Message::create([
                            'sender_id' => $reportingUser->id,
                            'recipient_id' => $admin->id,
                            'subject' => $subject,
                            'content' => $messageContent,
                            'is_abuse_report' => true,
                            'is_read' => false,
                            'is_draft' => false
                        ]);
                    }
                }
            }

            return back()->with('success', 'Signalement envoyé aux administrateurs.');

        } catch (\Exception $e) {
            Log::error("Erreur report : " . $e->getMessage());
            return back()->with('error', 'Erreur lors du signalement.');
        }
    }

    /**
     * Compatibilité ancien système
     */
    /**
     * Signale un avis (méthode de pont pour la compatibilité)
     * Cette méthode reçoit l'objet Review et redirige vers report() avec les bons paramètres
     */
    /**
     * Signale un avis (méthode de pont pour la compatibilité)
     */
    public function reportReview(Request $request, Review $review)
    {
        // On récupère l'ID de l'objet qui a été noté (l'article)
        $targetId = $review->reviewable_id;

        // On appelle report en précisant bien que c'est un 'item' (article)
        return $this->report($request, 'item', $targetId);
    }
}
