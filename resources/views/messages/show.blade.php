{{--
/**
 * Fichier: resources/views/messages/show.blade.php
 * Description: Vue pour afficher un message individuel avec ses détails et réponses
 *
 * Fonctionnalités:
 * - Affichage du sujet, expéditeur, destinataire
 * - Affichage du contenu du message
 * - Liste des réponses au message
 * - Options pour répondre et marquer comme important
 * - Modal pour répondre directement
 *
 * Variables requises:
 * - $message: Objet Message à afficher
 *
 * Routes associées:
 * - messages.show: Affiche un message spécifique
 * - messages.markImportant: Marque un message comme important
 * - messages.compose: Permet de répondre à un message
 */
--}}

@extends('layouts.layout')

@section('content')
    <div class="container" style="padding-top: 2rem; max-width: 1000px; margin: 0 auto;">
        <div class="service-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 style="color: var(--dark-color); margin-bottom: 0;">{{ $message->subject }}</h1>
                    @if($message->is_abuse_report)
                        <span class="badge bg-danger ms-2">Signalement</span>
                    @endif
                    @if($message->is_draft)
                        <span class="badge bg-secondary ms-2">Brouillon</span>
                    @endif
                </div>
                <div>
                    <small style="color: var(--text-color);">{{ $message->created_at->format('d/m/Y H:i') }}</small>
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div style="color: var(--text-color);">
                        <strong>De:</strong> {{ $message->sender->first_name }} {{ $message->sender->last_name }}
                        @if($message->sender->id == $message->recipient_id)
                            <span class="badge bg-info ms-2">À moi-même</span>
                        @endif
                    </div>
                    <div style="color: var(--text-color);">
                        <strong>À:</strong> {{ $message->recipient->first_name }} {{ $message->recipient->last_name }}
                    </div>
                </div>

                @if($message->item)
                    <div style="color: var(--text-color); margin-bottom: 1rem;">
                        <strong>Concernant:</strong>
                        <a href="{{ route('marketplace.items.show', $message->item) }}" style="color: var(--primary-color);">
                            {{ $message->item->title }}
                        </a>
                    </div>
                @endif
            </div>

            <div class="service-card p-3 mb-4" style="background-color: #f8f9fa; border-left: 4px solid var(--primary-color);">
                <p style="color: var(--text-color); white-space: pre-line; margin-bottom: 0;">
                    {{ $message->content }}
                </p>
            </div>

            @if($message->replies->count() > 0)
                <h5 style="color: var(--dark-color); margin: 1.5rem 0 1rem;">Réponses ({{ $message->replies->count() }})</h5>
                @foreach($message->replies as $reply)
                    <div class="service-card p-3 mb-3" style="background-color: #f8f9fa; border-left: 2px solid var(--secondary-color); margin-left: 20px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span style="font-weight: bold; color: var(--dark-color);">{{ $reply->sender->first_name }} {{ $reply->sender->last_name }}</span>
                                <small class="ms-2" style="color: var(--text-color);">{{ $reply->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                        <p style="color: var(--text-color); white-space: pre-line; margin-bottom: 0;">
                            {{ $reply->content }}
                        </p>
                    </div>
                @endforeach
            @endif

            @auth
                @if(auth()->id() === $message->recipient_id && !$message->is_abuse_report && !$message->is_draft)
                    <div class="mb-3">
                        <button type="button" class="btn btn-retro" data-bs-toggle="modal" data-bs-target="#replyModal">
                            <i class="bi bi-reply me-2"></i> Répondre
                        </button>

                        @if(!$message->is_important)
                            <form action="{{ route('messages.markImportant', $message) }}" method="POST" class="d-inline ms-2">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="bi bi-star me-1"></i> Marquer comme important
                                </button>
                            </form>
                        @endif
                    </div>
                @endif

                @if(auth()->id() === $message->sender_id && !$message->is_draft)
                    <div class="mb-3">
                        <a href="{{ route('messages.compose', ['reply_to' => $message->id]) }}" class="btn btn-retro">
                            <i class="bi bi-reply me-2"></i> Répondre
                        </a>
                    </div>
                @endif
            @endauth

            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('messages.inbox') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i> Retour à la boîte de réception
                </a>
            </div>
        </div>
    </div>

    <!-- Modal pour répondre -->
    @auth
        @if(auth()->id() === $message->recipient_id && !$message->is_abuse_report)
            <div class="modal fade" id="replyModal" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="replyModalLabel">Répondre à: {{ $message->subject }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route('messages.store') }}">
                                @csrf
                                {{-- Champs cachés --}}
                                <input type="hidden" name="recipient_id" value="{{ $message->sender_id }}">
                                <input type="hidden" name="parent_id" value="{{ $message->id }}">
                                @if($message->item_id)
                                    <input type="hidden" name="item_id" value="{{ $message->item_id }}">
                                @endif

                                {{-- Sujet --}}
                                <div class="mb-3">
                                    <label for="replySubject" class="form-label">Sujet</label>
                                    <input type="text" class="form-control" id="replySubject"
                                           name="subject" value="RE: {{ $message->subject }}" required>
                                </div>

                                {{-- Contenu --}}
                                <div class="mb-3">
                                    <label for="replyContent" class="form-label">Votre réponse</label>
                                    <textarea class="form-control" id="replyContent" name="content" rows="5" required></textarea>
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="notifyEmail" name="notify_email" value="1" checked>
                                    <label class="form-check-label" for="notifyEmail">
                                        Recevoir une notification par email pour les réponses
                                    </label>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send me-2"></i> Envoyer la réponse
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endauth
@endsection
