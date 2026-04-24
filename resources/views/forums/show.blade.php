@extends('layouts.layout')

@section('content')
    <div class="main-content">
        <div class="container">
            <!-- Affichage du forum -->
            <div class="forum-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1>{{ $forum->name }}</h1>
                    @auth
                        @if(Auth::user()->isAdmin())
                            <div class="d-flex gap-2">
                                <!-- Bouton pour gérer les sujets -->
                                <a href="{{ route('admin.forums.topics.index', $forum) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-list me-1"></i> Gérer les sujets
                                </a>
                            </div>
                        @endif
                    @endauth
                </div>
                <p>{{ $forum->description }}</p>
            </div>

            <!-- Liste des sujets -->
            <h2>Sujets</h2>
            @foreach($topics as $topic)
                <div class="topic-card mb-3 p-3 bg-white rounded shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h3 class="mb-0">
                            <a href="{{ route('forums.topics.show', [$forum, $topic]) }}">
                                {{ $topic->title }}
                            </a>
                            @if($topic->is_locked)
                                <span class="badge bg-warning text-dark ms-2">
                                    <i class="fas fa-lock me-1"></i> Verrouillé
                                </span>
                            @endif
                        </h3>
                    </div>

                    <div class="mb-3">
                        <p>{!! $parsedown->text(Str::limit($topic->content, 200)) !!}</p>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <p class="mb-0">
                            @if($topic->user)
                                Créé par {{ $topic->user->first_name ?? '' }} {{ $topic->user->last_name ?? $topic->user->name }} le
                            @else
                                Créé par un utilisateur inconnu le
                            @endif
                            @if($topic->created_at)
                                {{ $topic->created_at->format('d/m/Y H:i') }}
                            @else
                                Date inconnue
                            @endif
                            | {{ $topic->replies_count }} réponses
                        </p>
                    </div>
                </div>
            @endforeach

            <!-- Formulaire pour créer un nouveau sujet -->
            @auth
                <div class="mt-4 bg-white p-4 rounded shadow-sm">
                    <h3 class="mb-3">Créer un nouveau sujet</h3>
                    <form id="topic-form" action="{{ route('forums.topics.store', $forum) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Contenu</label>
                            <textarea class="form-control" id="topic-content" name="content" rows="5" required></textarea>
                            <small class="form-text text-muted">
                                Vous pouvez utiliser le <a href="https://www.markdownguide.org/basic-syntax/" target="_blank">Markdown</a> pour formater votre texte.
                            </small>
                        </div>
                        <button type="button" id="submit-topic" class="btn btn-retro">Créer le sujet</button>
                    </form>
                </div>
                <!-- Intégration de SimpleMDE pour l'éditeur Markdown -->
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simplemde@1.11.2/dist/simplemde.min.css">
                <script src="https://cdn.jsdelivr.net/npm/simplemde@1.11.2/dist/simplemde.min.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Initialisation de SimpleMDE pour le contenu du sujet
                        var editor = new SimpleMDE({
                            element: document.getElementById("topic-content"),
                            spellChecker: false,
                            placeholder: "Votre contenu...",
                            autofocus: true,
                            toolbar: [
                                'bold', 'italic', 'heading', '|',
                                'quote', 'unordered-list', 'ordered-list', '|',
                                'link', 'image', '|',
                                'preview', 'side-by-side', 'fullscreen'
                            ]
                        });

                        // Gestion de la soumission du formulaire
                        document.getElementById('submit-topic').addEventListener('click', function() {
                            // Récupère le contenu de l'éditeur SimpleMDE
                            var content = editor.value();
                            if (!content.trim()) {
                                alert("Veuillez entrer un contenu pour le sujet.");
                                return;
                            }

                            // Met à jour la valeur du textarea avec le contenu de l'éditeur
                            document.getElementById('topic-content').value = content;

                            // Soumet le formulaire
                            document.getElementById('topic-form').submit();
                        });
                    });
                </script>
            @else
                <div class="alert alert-info mt-4">
                    Vous devez être connecté pour créer un sujet. <a href="{{ route('login') }}">Se connecter</a>
                </div>
            @endauth
        </div>
    </div>

    <!-- CSS pour les cartes de sujet -->
    <style>
        .topic-card {
            border-left: 4px solid #dee2e6;
            position: relative;
        }

        .topic-card:nth-child(even) {
            border-left-color: #0d6efd;
        }

        .topic-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -4px;
            width: 4px;
            height: 100%;
        }

        .badge.bg-warning.text-dark {
            font-size: 0.8em;
            padding: 0.25em 0.5em;
        }
    </style>
@endsection
