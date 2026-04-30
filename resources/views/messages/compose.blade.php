{{--
/**
 * Fichier: resources/views/messages/compose.blade.php
 * Description: Vue pour composer un nouveau message ou répondre à un message existant
 *
 * Fonctionnalités:
 * - Formulaire de composition de message avec champs pour destinataire, sujet et contenu
 * - Pré-remplissage automatique pour les réponses (RE: sujet original)
 * - Affichage du message original en cas de réponse
 * - Options pour enregistrer comme brouillon ou recevoir des notifications par email
 * - Gestion des cas particuliers (réponse à un message, message lié à un item)
 *
 * Variables requises:
 * - $users: Collection d'utilisateurs (destinataires potentiels) - utilisé si pas de destinataire prédéfinis
 * - $replyTo: Objet Message (optionnel) - le message auquel on répond
 * - $item: Objet Item (optionnel) - l'item associé au message
 *
 * Routes associées:
 * - messages.store: Soumet le formulaire pour créer/enregistrer le message
 * - messages.inbox: Lien pour retourner à la boîte de réception
 *
 * Comportements spéciaux:
 * - Si $replyTo existe: masques les champs destinataire et item_id, pré-remplit le sujet
 * - Si request()->has('user_id'): masques le sélecteur de destinataire
 * - Si $item existe: ajoute un champ caché item_id
 */
--}}

@extends('layouts.layout')

@section('content')
    <div class="container" style="padding-top: 2rem; max-width: 1000px; margin: 0 auto;">
        <h1 style="color: var(--dark-color); margin-bottom: 1.5rem;">
            {{ isset($replyTo) ? 'Répondre à un message' : 'Nouveau message' }}
        </h1>

        @if(isset($error))
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="service-card p-4">
            <form method="POST" action="{{ route('messages.store') }}">
                @csrf

                {{-- Champs cachés --}}
                @if(isset($replyTo))
                    <input type="hidden" name="parent_id" value="{{ $replyTo->id }}">
                    <input type="hidden" name="recipient_id" value="{{ $replyTo->sender_id }}">
                    @if($replyTo->item_id)
                        <input type="hidden" name="item_id" value="{{ $replyTo->item_id }}">
                    @endif
                @elseif(request()->has('user_id'))
                    <input type="hidden" name="recipient_id" value="{{ request()->user_id }}">
                @endif

                @if(isset($item) && !isset($replyTo))
                    <input type="hidden" name="item_id" value="{{ $item->id }}">
                @endif

                {{-- Sujet --}}
                <div class="mb-3">
                    <label for="subject" class="form-label" style="color: var(--dark-color);">Sujet</label>
                    <input type="text" class="form-control" id="subject" name="subject"
                           value="{{ old('subject', isset($replyTo) ? 'RE: ' . $replyTo->subject : (isset($item) ? 'Question concernant ' . $item->title : '')) }}"
                           required>
                </div>

                {{-- Destinataire (uniquement si pas de replyTo et pas de user_id dans la requête) --}}
                @if(!isset($replyTo) && !request()->has('user_id'))
                    <div class="mb-3">
                        <label for="recipient" class="form-label" style="color: var(--dark-color);">Destinataire</label>
                        <select class="form-select" id="recipient" name="recipient_id" required>
                            <option value="">Sélectionnez un destinataire</option>
                            @foreach($users ?? [] as $user)
                                <option value="{{ $user->id }}"
                                    {{ (old('recipient_id') == $user->id) ? 'selected' : '' }}>
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Contenu --}}
                <div class="mb-3">
                    <label for="content" class="form-label" style="color: var(--dark-color);">Message</label>
                    <textarea class="form-control" id="content" name="content" rows="8" required>{{ old('content') }}</textarea>
                </div>

                {{-- Message original (si réponse) --}}
                @isset($replyTo)
                    <div class="mb-3">
                        <h6 style="color: var(--dark-color);">Message original:</h6>
                        <div class="service-card p-3" style="background-color: #f8f9fa; border-left: 2px solid var(--secondary-color);">
                            <p style="color: var(--text-color); white-space: pre-line; margin-bottom: 0;">
                                {{ $replyTo->content }}
                            </p>
                            <small style="color: var(--text-color);">
                                Envoyé par {{ $replyTo->sender->first_name }} {{ $replyTo->sender->last_name }}
                                le {{ $replyTo->created_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    </div>
                @endisset

                {{-- Options --}}
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="saveDraft" name="save_as_draft" value="1"
                        {{ old('save_as_draft') ? 'checked' : '' }}>
                    <label class="form-check-label" for="saveDraft" style="color: var(--dark-color);">
                        Enregistrer comme brouillon
                    </label>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="notifyEmail" name="notify_email" value="1"
                        {{ old('notify_email', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="notifyEmail" style="color: var(--dark-color);">
                        Recevoir une notification par email pour les réponses
                    </label>
                </div>

                {{-- Boutons --}}
                <div class="d-flex justify-content-between">
                    <a href="{{ route('messages.inbox') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-retro">
                        <i class="bi bi-send me-2"></i>
                        {{ isset($replyTo) ? 'Envoyer la réponse' : 'Envoyer le message' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
