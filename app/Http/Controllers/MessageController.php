<?php
// app/Http/Controllers/MessageController.php
namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class MessageController extends Controller
{
    public function inbox()
    {
        $user = Auth::user();

        $receivedMessages = Message::where('recipient_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $sentMessages = Message::where('sender_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $drafts = Message::where('sender_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $reports = Message::where('recipient_id', $user->id)
            ->where('is_abuse_report', true)
            ->orderBy('created_at', 'desc')
            ->get();

        $unreadCount = Message::where('recipient_id', $user->id)
            ->where('is_read', false)
            ->count();

        return view('messages.inbox', compact('receivedMessages', 'sentMessages', 'drafts', 'reports', 'unreadCount'));
    }

    public function show(Message $message)
    {
        // Marquer comme lu si le destinataire est l'utilisateur actuel
        if ($message->recipient_id == Auth::id() && !$message->is_read && !$message->is_draft) {
            $message->update(['is_read' => true]);
        }

        return view('messages.show', compact('message'));
    }

    public function compose(Request $request)
    {
        try {
            $users = User::where('id', '!=', Auth::id())
                ->select('id', 'first_name', 'last_name')
                ->get();

            $replyTo = null;
            $item = null;

            if ($request->has('reply_to')) {
                $replyTo = Message::with(['sender', 'recipient'])->findOrFail($request->reply_to);
            }

            if ($request->has('user_id')) {
                $user = User::findOrFail($request->user_id);
            }

            if ($request->has('item_id')) {
                $item = \App\Models\Item::findOrFail($request->item_id);
            }

            return view('messages.compose', compact('users', 'replyTo', 'item'));

        } catch (\Exception $e) {
            // En cas d'erreur, retourner une vue avec un message d'erreur
            return view('messages.compose', [
                'users' => collect(),
                'replyTo' => null,
                'item' => null,
                'error' => 'Une erreur est survenue : ' . $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'item_id' => 'nullable|exists:items,id',
            'parent_id' => 'nullable|exists:messages,id',
            'save_as_draft' => 'boolean',
            'notify_email' => 'boolean'
        ]);

        // Créer le message
        $messageData = [
            'sender_id' => Auth::id(),
            'recipient_id' => $validated['recipient_id'],
            'subject' => $validated['subject'],
            'content' => $validated['content'],
            'is_read' => false,
            'is_draft' => $request->has('save_as_draft') && $request->save_as_draft === true
        ];

        // Ajouter les champs optionnels s'ils existent
        if ($request->has('item_id') && !empty($validated['item_id'])) {
            $messageData['item_id'] = $validated['item_id'];
        }

        if ($request->has('parent_id') && !empty($validated['parent_id'])) {
            $messageData['parent_id'] = $validated['parent_id'];
        }

        $message = Message::create($messageData);

        // Envoyer une notification au destinataire si ce n'est pas un brouillon
        if (!$message->is_draft) {
            try {
                $recipient = User::find($validated['recipient_id']);
                if ($recipient) {
                    $recipient->notify(new NewMessageNotification($message));
                }
            } catch (\Exception $e) {
                \Log::error("Erreur lors de l'envoi de la notification: " . $e->getMessage());
            }
        }

        // Vérifier si la requête est AJAX
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message->is_draft ? 'Brouillon enregistré avec succès' : 'Message envoyé avec succès',
                'redirect' => route('messages.inbox')
            ]);
        }

        return redirect()->route('messages.inbox')
            ->with('success', $message->is_draft ? 'Brouillon enregistré avec succès' : 'Message envoyé avec succès');
    }


    public function markAsRead(Message $message)
    {
        if ($message->recipient_id === Auth::id()) {
            $message->update(['is_read' => true]);
        }

        return back();
    }

    public function markImportant(Message $message)
    {
        if ($message->recipient_id === Auth::id()) {
            $message->update(['is_important' => true]);
        }

        return back()->with('success', 'Message marqué comme important');
    }
}
