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
                <h1>{{ $topic->title }}</h1>
                <div class="my-3">{!! $parsedown->text($topic->content) !!}</div>
                <p class="text-muted">
                    Créé par <strong>{{ $topic->user->name ?? 'Utilisateur inconnu' }}</strong>
                    le {{ $topic->created_at ? $topic->created_at->format('d/m/Y H:i') : 'Date inconnue' }}
                </p>
            </div>

            <!-- Liste des réponses -->
            <h2>Réponses</h2>
            @forelse($replies as $reply)
                <div class="reply-card p-3 mb-3 border rounded">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>{{ $reply->user->name ?? 'Utilisateur inconnu' }}</strong>
                            <small class="text-muted ms-2">
                                {{ $reply->created_at ? $reply->created_at->format('d/m/Y H:i') : 'Date inconnue' }}
                            </small>
                        </div>
                        <div>
                            @auth
                                <form action="{{ route('forum.replies.like', $reply) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn-like p-0 bg-transparent border-0">
                                        @if($reply->likes->contains('user_id', Auth::id()))
                                            <i class="fas fa-thumbs-up text-primary"></i> {{ $reply->likes->count() }}
                                        @else
                                            <i class="far fa-thumbs-up"></i> {{ $reply->likes->count() }}
                                        @endif
                                    </button>
                                </form>
                            @endauth
                        </div>
                    </div>
                    <div class="mt-2">
                        <p>{!! $parsedown->text($reply->content) !!}</p>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">
                    Aucune réponse pour ce sujet.
                </div>
            @endforelse

            <!-- Formulaire pour répondre -->
            @auth
                <div class="mt-4">
                    <h3>Répondre</h3>
                    <form id="reply-form" action="{{ route('forums.topics.replies.store', [$forum, $topic]) }}" method="POST">
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
                <!-- Intégration de SimpleMDE pour l'éditeur Markdown -->
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simplemde@1.11.2/dist/simplemde.min.css">
                <script src="https://cdn.jsdelivr.net/npm/simplemde@1.11.2/dist/simplemde.min.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Initialisation de SimpleMDE
                        var editor = new SimpleMDE({
                            element: document.getElementById("reply-content"),
                            spellChecker: false,
                            placeholder: "Votre réponse...",
                            autofocus: true
                        });

                        // Gestion de la soumission du formulaire
                        document.getElementById('submit-reply').addEventListener('click', function() {
                            // Récupère le contenu de l'éditeur SimpleMDE
                            var content = editor.value();
                            if (!content.trim()) {
                                alert("Veuillez entrer une réponse.");
                                return;
                            }

                            // Met à jour la valeur du textarea avec le contenu de l'éditeur
                            document.getElementById('reply-content').value = content;

                            // Soumet le formulaire
                            document.getElementById('reply-form').submit();
                        });
                    });
                </script>
            @else
                <div class="alert alert-info mt-4">
                    Vous devez être connecté pour répondre. <a href="{{ route('login') }}">Se connecter</a>
                </div>
            @endauth
        </div>
    </div>
    
@endsection
