@extends('layouts.layout')

@section('content')
    <div class="main-content">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Affichage du sujet -->
            <div class="topic-card p-4 mb-4 bg-white rounded shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <h1 class="mb-0 me-3">{{ $topic->title }}</h1>
                        @if($topic->is_locked)
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-lock me-1"></i> Verrouillé
                            </span>
                        @endif
                    </div>

                    @auth
                        @if(Auth::user()->isAdmin())
                            <div class="d-flex gap-2">
                                @if($topic->is_locked)
                                    <form action="{{ route('admin.forums.topics.unlock', $topic) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Déverrouiller ce sujet">
                                            <i class="fas fa-lock-open me-1"></i> Déverrouiller
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.forums.topics.lock', $topic) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning" title="Verrouiller ce sujet">
                                            <i class="fas fa-lock me-1"></i> Verrouiller
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.forums.topics.destroy', $topic) }}" method="POST" onsubmit="return confirm('Supprimer ce sujet ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                        <i class="fas fa-trash me-1"></i> Supprimer
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>

                <!-- Contenu du sujet -->
                @if(!$topic->is_locked || (Auth::check() && Auth::user()->isAdmin()))
                    <div class="my-3 post-content">{!! $parsedown->text($topic->content) !!}</div>

                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                        <div class="d-flex align-items-center">
                            @php
                                $topicUser = $topic->user;
                                $topicAvatar = $topicUser->avatar_url ?? 'https://via.placeholder.com/60';
                            @endphp

                            <div>
                                <div>
                                    <strong>{{ $topicUser->first_name ?? '' }} {{ $topicUser->last_name ?? $topicUser->name ?? 'Utilisateur inconnu' }}</strong>
                                </div>
                                <small class="text-muted">
                                    Posté le {{ $topic->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-lock me-2"></i> Ce sujet est verrouillé et réservé aux administrateurs.
                    </div>
                @endif
            </div>

            <!-- Liste des réponses -->
            @if(!$topic->is_locked || (Auth::check() && Auth::user()->isAdmin()))
                <h2 class="mb-3">Réponses ({{ $replies->count() }})</h2>

                @forelse($replies as $reply)
                    @php
                        $replyUser = $reply->user;
                        $replyAvatar = $replyUser->avatar_url ?? 'https://via.placeholder.com/50';
                    @endphp
                    <div class="reply-card p-3 mb-3 bg-white rounded shadow-sm">
                        <div class="d-flex align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong>{{ $replyUser->first_name ?? '' }} {{ $replyUser->last_name ?? $replyUser->name ?? 'Utilisateur inconnu' }}</strong>
                                        <small class="text-muted ms-2">
                                            {{ $reply->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>

                                    <div class="d-flex gap-2">
                                        @auth
                                            <!-- Bouton Like -->
                                            <form action="{{ route('forum.replies.like', $reply) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-secondary p-1" title="J'aime">
                                                    @if($reply->likes->contains('user_id', Auth::id()))
                                                        <i class="fas fa-thumbs-up text-primary"></i> <span>{{ $reply->likes->count() }}</span>
                                                    @else
                                                        <i class="far fa-thumbs-up"></i> <span>{{ $reply->likes->count() }}</span>
                                                    @endif
                                                </button>
                                            </form>

                                            <!-- Bouton Citer -->
                                            <a href="{{ route('forums.replies.quote', [$topic, $reply]) }}" class="btn btn-sm btn-outline-secondary p-1" title="Citer">
                                                <i class="fas fa-quote-left"></i>
                                            </a>

                                            <!-- Bouton Signaler -->
                                            @if(Auth::id() !== $reply->user_id)
                                                <button type="button" class="btn btn-sm btn-outline-danger p-1" title="Signaler"
                                                        onclick="openReportModal('{{ $reply->id }}')">
                                                    <i class="fas fa-flag"></i>
                                                </button>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                                <div class="post-content mb-3">
                                    {!! $parsedown->text($reply->content) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal pour signaler un commentaire -->
                    <div id="reportModal-{{ $reply->id }}" class="custom-modal">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5>Signaler un commentaire</h5>
                                <button type="button" class="btn-close" onclick="closeReportModal('{{ $reply->id }}')">×</button>
                            </div>
                            <form method="POST" action="{{ route('reviews.report', ['type' => 'reply', 'id' => $reply->id]) }}">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Raison du signalement</label>
                                        <select class="form-select" name="reason" required>
                                            <option value="" selected disabled>Sélectionnez une raison</option>
                                            <option value="spam">Contenu indésirable ou spam</option>
                                            <option value="abusive">Langage abusif ou haineux</option>
                                            <option value="off_topic">Hors sujet</option>
                                            <option value="duplicate">Doublon</option>
                                            <option value="other">Autre</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Commentaire (optionnel)</label>
                                        <textarea class="form-control" name="comment" rows="3"></textarea>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="notify_admin" value="1" checked>
                                        <label class="form-check-label">Notifier l'administrateur</label>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" onclick="closeReportModal('{{ $reply->id }}')">Annuler</button>
                                    <button type="submit" class="btn btn-primary">Envoyer le signalement</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info">
                        Aucune réponse pour ce sujet. Soyez le premier à répondre !
                    </div>
                @endforelse

                <!-- Formulaire pour répondre -->
                @auth
                    @if(!$topic->is_locked)
                        <div class="mt-4 bg-white p-4 rounded shadow-sm">
                            <h3 class="mb-3">Répondre</h3>
                            <div class="d-flex align-items-start mb-3">
                                @php
                                    $authUser = Auth::user();
                                    $authAvatar = $authUser->avatar_url ?? 'https://via.placeholder.com/50';
                                @endphp

                                <div class="flex-grow-1">
                                    <form id="reply-form" action="{{ route('forums.topics.replies.store', [$topic->forum, $topic]) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <textarea class="form-control" id="reply-content" name="content" rows="5" placeholder="Votre réponse..." required></textarea>
                                            <small class="form-text text-muted">
                                                Vous pouvez utiliser le <a href="https://www.markdownguide.org/basic-syntax/" target="_blank">Markdown</a> pour formater votre texte.
                                            </small>
                                        </div>
                                        <button type="button" id="submit-reply" class="btn btn-retro">Répondre</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Intégration de SimpleMDE pour l'éditeur Markdown -->
                        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simplemde@1.11.2/dist/simplemde.min.css">
                        <script src="https://cdn.jsdelivr.net/npm/simplemde@1.11.2/dist/simplemde.min.js"></script>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var editor = new SimpleMDE({
                                    element: document.getElementById("reply-content"),
                                    spellChecker: false,
                                    placeholder: "Votre réponse...",
                                    autofocus: true,
                                    forceSync: true,
                                    toolbar: [
                                        'bold', 'italic', 'heading', '|',
                                        'quote', 'unordered-list', 'ordered-list', '|',
                                        'link', 'image', '|',
                                        'preview', 'side-by-side', 'fullscreen'
                                    ]
                                });

                                // Gestion de la soumission du formulaire
                                document.getElementById('submit-reply').addEventListener('click', function() {
                                    var content = editor.value();
                                    if (!content.trim()) {
                                        alert("Veuillez entrer une réponse.");
                                        return;
                                    }

                                    document.getElementById('reply-content').value = content;
                                    document.getElementById('reply-form').submit();
                                });
                            });
                        </script>
                    @else
                        <div class="alert alert-info mt-4">
                            Ce sujet est verrouillé. Seuls les administrateurs peuvent y répondre.
                        </div>
                    @endif
                @else
                    <div class="alert alert-info mt-4">
                        Vous devez être connecté pour répondre.
                        <a href="{{ route('login') }}" class="btn btn-sm btn-primary ms-2">Se connecter</a>
                    </div>
                @endauth
            @else
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-lock me-2"></i> Ce sujet est verrouillé et réservé aux administrateurs.
                </div>
            @endif
        </div>
    </div>

    <!-- CSS pour les citations, les cartes de réponse et les modales -->
    <style>
        .post-content blockquote {
            font-style: italic;
            color: #555;
            border-left: 3px solid #ccc;
            padding-left: 15px;
            margin: 10px 0;
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 0 3px 3px 0;
        }

        .reply-card {
            border-left: 4px solid #dee2e6;
            position: relative;
        }

        .reply-card:nth-child(even) {
            border-left-color: #0d6efd;
        }

        .reply-card img, .topic-card img {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }

        .topic-card img {
            width: 60px;
            height: 60px;
        }

        .post-content {
            line-height: 1.6;
        }

        .badge {
            font-size: 0.9em;
            padding: 0.35em 0.65em;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .btn-outline-secondary {
            color: #6c757d;
            border-color: #dee2e6;
        }

        .btn-outline-secondary:hover {
            color: #0d6efd;
            border-color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.1);
        }

        /* Style pour les boutons de verrouillage */
        .btn-warning {
            color: #856404;
            background-color: #fff3cd;
            border-color: #ffeeba;
        }

        .btn-warning:hover {
            color: #664d03;
            background-color: #ffe69c;
            border-color: #ffd663;
        }

        .btn-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .btn-success:hover {
            color: #155724;
            background-color: #c3e6cb;
            border-color: #b1dfbb;
        }

        /* Style pour les modales personnalisées */
        .custom-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow: auto;
        }

        .custom-modal .modal-content {
            background-color: white;
            margin: auto;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .custom-modal .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .custom-modal .modal-body {
            margin-bottom: 15px;
        }

        .custom-modal .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0;
            line-height: 1;
        }

        /* Style pour le bouton Signaler */
        .btn-outline-danger {
            color: #dc3545;
            border-color: #f5c2c7;
        }

        .btn-outline-danger:hover {
            color: #fff;
            background-color: #dc3545;
            border-color: #dc3545;
        }
    </style>

    <!-- JavaScript pour les modales et SimpleMDE -->
    <script>
        // Fonctions globales pour les modales
        function openReportModal(id) {
            const modal = document.getElementById('reportModal-' + id);
            if(modal) modal.style.display = 'block';
        }

        function closeReportModal(id) {
            const modal = document.getElementById('reportModal-' + id);
            if(modal) modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('custom-modal')) {
                event.target.style.display = 'none';
            }
        }

        // Initialisation SimpleMDE
        document.addEventListener('DOMContentLoaded', function() {
            const textArea = document.getElementById("reply-content");
            if (textArea) {
                var editor = new SimpleMDE({
                    element: textArea,
                    spellChecker: false,
                    forceSync: true,
                    toolbar: ["bold", "italic", "heading", "|", "quote", "unordered-list", "ordered-list", "|", "link", "preview", "side-by-side", "fullscreen"]
                });

                const submitBtn = document.getElementById('submit-reply');
                if (submitBtn) {
                    submitBtn.addEventListener('click', function() {
                        if (!editor.value().trim()) {
                            alert("Veuillez entrer une réponse.");
                            return;
                        }
                        document.getElementById('reply-form').submit();
                    });
                }
            }
        });
    </script>
@endsection
