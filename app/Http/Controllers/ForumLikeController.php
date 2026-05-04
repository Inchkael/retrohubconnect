<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForumReply;
use App\Models\ForumLike;
use Illuminate\Support\Facades\Auth;

class ForumLikeController extends Controller
{
    // Ajouter ou supprimer un "like" sur une réponse
    public function toggleLike(Request $request, ForumReply $reply)
    {
        $user = Auth::user();
        $existingLike = ForumLike::where('reply_id', $reply->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingLike) {
            // Si le "like" existe déjà, le supprimer
            $existingLike->delete();
            $message = 'Like retiré.';
        } else {
            // Sinon, créer un nouveau "like"
            ForumLike::create([
                'reply_id' => $reply->id,
                'user_id' => $user->id,
            ]);
            $message = 'Like ajouté !';
        }

        return back()->with('success', $message);
    }
}
