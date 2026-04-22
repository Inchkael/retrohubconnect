@extends('layouts.layout')

@section('content')
    <div class="main-content">
        <div class="container">
            <h1 class="mb-4">Créer un nouveau sujet dans {{ $forum->name }}</h1>

            <form id="create-topic-form" action="{{ route('forums.topics.store', $forum) }}" method="POST">
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
                <button type="submit" id="submit-topic" class="btn btn-retro">Créer le sujet</button>
            </form>

            <!-- Intégration de SimpleMDE pour l'éditeur Markdown -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simplemde@1.11.2/dist/simplemde.min.css">
            <script src="https://cdn.jsdelivr.net/npm/simplemde@1.11.2/dist/simplemde.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var editor = new SimpleMDE({
                        element: document.getElementById("topic-content"),
                        spellChecker: false,
                        placeholder: "Votre contenu...",
                        autofocus: true,
                        forceSync: true
                    });

                    document.getElementById('create-topic-form').addEventListener('submit', function(e) {
                        editor.toTextArea();
                        editor.save();
                    });
                });
            </script>
        </div>
    </div>
@endsection
