{{--
/**
 * Fichier: resources/views/messages/inbox.blade.php
 * Description: Vue principale de la boîte de réception des messages
 *
 * Fonctionnalités:
 * - Affichage des messages reçus, envoyés, brouillons et signalements via des onglets
 * - Indicateurs visuels pour les messages non lus
 * - Bouton pour composer un nouveau message
 * - Inclusion dynamique des cartes de message (message-card)
 * - Gestion des états vides (aucun message)
 *
 * Variables requises:
 * - $receivedMessages: Collection de messages reçus par l'utilisateur
 * - $sentMessages: Collection de messages envoyés par l'utilisateur
 * - $drafts: Collection de brouillons de l'utilisateur
 * - $reports: Collection de messages signalés (pour les admins)
 * - $unreadCount: Nombre de messages non lus
 *
 * Routes associées:
 * - messages.inbox: Affiche la boîte de réception
 * - messages.compose: Lien pour créer un nouveau message
 *
 * Partiels utilisés:
 * - messages.partials.message-card: Affiche une carte de message individuelle
 */
--}}

@extends('layouts.layout')

@section('content')
    <div class="container" style="padding-top: 2rem; max-width: 1200px; margin: 0 auto;">
        <h1 style="color: var(--dark-color); margin-bottom: 1.5rem;">Messagerie</h1>

        <div class="service-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 style="color: var(--dark-color); margin-bottom: 0;">Boîte de réception</h2>
                <a href="{{ route('messages.compose') }}" class="btn-retro">
                    <i class="bi bi-pencil-square me-2"></i>Nouveau message
                </a>
            </div>

            <ul class="nav nav-tabs mb-3" id="messageTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="inbox-tab" data-bs-toggle="tab" data-bs-target="#inbox" type="button" role="tab">
                        Reçus
                        @if($unreadCount > 0)
                            <span class="badge bg-danger ms-1">{{ $unreadCount }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="sent-tab" data-bs-toggle="tab" data-bs-target="#sent" type="button" role="tab">Envoyés</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="drafts-tab" data-bs-toggle="tab" data-bs-target="#drafts" type="button" role="tab">Brouillons</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reports-tab" data-bs-toggle="tab" data-bs-target="#reports" type="button" role="tab">Signalements</button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Messages reçus -->
                <div class="tab-pane fade show active" id="inbox" role="tabpanel">
                    @if($receivedMessages->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-inbox" style="font-size: 2rem; color: var(--secondary-color); margin-bottom: 1rem;"></i>
                            <p style="color: var(--text-color);">Aucun message reçu</p>
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($receivedMessages as $message)
                                @include('messages.partials.message-card', ['message' => $message])
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Messages envoyés -->
                <div class="tab-pane fade" id="sent" role="tabpanel">
                    @if($sentMessages->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-send" style="font-size: 2rem; color: var(--secondary-color); margin-bottom: 1rem;"></i>
                            <p style="color: var(--text-color);">Aucun message envoyé</p>
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($sentMessages as $message)
                                @include('messages.partials.message-card', ['message' => $message])
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Brouillons -->
                <div class="tab-pane fade" id="drafts" role="tabpanel">
                    @if($drafts->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-file-earmark" style="font-size: 2rem; color: var(--secondary-color); margin-bottom: 1rem;"></i>
                            <p style="color: var(--text-color);">Aucun brouillon</p>
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($drafts as $message)
                                @include('messages.partials.message-card', ['message' => $message])
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Signalements -->
                <div class="tab-pane fade" id="reports" role="tabpanel">
                    @if($reports->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-flag" style="font-size: 2rem; color: var(--secondary-color); margin-bottom: 1rem;"></i>
                            <p style="color: var(--text-color);">Aucun signalement</p>
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($reports as $message)
                                @include('messages.partials.message-card', ['message' => $message])
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
