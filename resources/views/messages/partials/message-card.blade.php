{{--
/**
 * Fichier: resources/views/messages/partials/message-card.blade.php
 * Description: Partiel Blade pour afficher une carte de message dans les listes
 *
 * Fonctionnalités:
 * - Affichage compact d'un message avec son expéditeur/destinataire
 * - Indicateurs visuels pour les différents états (non lu, brouillon, signalement)
 * - Icônes différenciées selon le type de message
 * - Aperçu du contenu tronqué
 * - Lien vers la vue détaillée du message
 *
 * Variables requises:
 * - $message: Objet Message à afficher (requis)
 *
 * États gérés:
 * - $isUnread: Message non lu (calculé: !is_read && recipient_id == auth()->id() && !is_draft)
 * - $isSent: Message envoyé par l'utilisateur connecté (calculé: sender_id == auth()->id())
 * - $isDraft: Message en brouillon (calculé: is_draft)
 * - $isReport: Message de signalement (calculé: is_abuse_report)
 *
 * Comportements:
 * - Applique un style gras aux messages non lus
 * - Affiche des icônes différentes selon le contexte (brouillon, signalement, envoyé/reçu)
 * - Tronque le contenu à 100 caractères avec Str::limit()
 * - Utilise des badges colorés pour les états spéciaux
 *
 * Utilisation:
 * @include('messages.partials.message-card', ['message' => $message])
 */
--}}

@php
    $isUnread = !$message->is_read && $message->recipient_id == auth()->id() && !$message->is_draft;
    $isSent = $message->sender_id == auth()->id();
    $isDraft = $message->is_draft;
    $isReport = $message->is_abuse_report;
@endphp

<a href="{{ route('messages.show', $message) }}" class="list-group-item list-group-item-action {{ $isUnread ? 'fw-bold' : '' }}">
    <div class="d-flex w-100 justify-content-between">
        <div>
            <h6 class="mb-1 d-flex align-items-center">
                @if($isDraft)
                    <i class="bi bi-file-earmark me-2"></i> Brouillon
                @elseif($isReport)
                    <i class="bi bi-flag me-2 text-danger"></i> Signalement
                @else
                    @if($isSent)
                        <i class="bi bi-send me-2"></i> À: {{ $message->recipient->first_name }} {{ $message->recipient->last_name }}
                    @else
                        <i class="bi bi-person me-2"></i> {{ $message->sender->first_name }} {{ $message->sender->last_name }}
                    @endif
                @endif
            </h6>
            <p class="mb-1">{{ $message->subject }}</p>
        </div>
        <div class="text-end">
            <small class="text-muted">{{ $message->created_at->format('d/m/Y') }}</small>
            @if($isUnread)
                <span class="badge bg-primary ms-2">Nouveau</span>
            @endif
            @if($isReport)
                <span class="badge bg-danger ms-2">Signalement</span>
            @endif
        </div>
    </div>
    <small class="text-muted">{!! Str::limit($message->content, 100) !!}</small>
</a>
