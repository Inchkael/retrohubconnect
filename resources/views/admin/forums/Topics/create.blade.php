@extends('layouts.layout')

@section('content')
    <div class="main-content">
        <div class="container">
            <h1 class="mb-4">Créer un nouveau sujet dans {{ $forum->name }}</h1>

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('admin.forums.topics.store', $forum) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre</label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Contenu</label>
                            <textarea class="form-control" id="topic-content" name="content" rows="10" required>{{ old('content') }}</textarea>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_locked" name="is_locked" {{ old('is_locked') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_locked">Verrouiller ce sujet</label>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.forums.topics.index', $forum) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-retro">
                                <i class="fas fa-save me-1"></i> Créer le sujet
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Intégration de SimpleMDE pour l'éditeur Markdown -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simplemde@1.11.2/dist/simplemde.min.css">
            <script src="https://cdn.jsdelivr.net/npm/simplemde@1.11.2/dist/simplemde.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var editor = new SimpleMDE({
                        element: document.getElementById("topic-content"),
                        spellChecker: false,
                        placeholder: "Votre contenu...",
                        toolbar: [
                            'bold', 'italic', 'heading', '|',
                            'quote', 'unordered-list', 'ordered-list', '|',
                            'link', 'image', '|',
                            'preview', 'side-by-side', 'fullscreen'
                        ]
                    });
                });
            </script>
        </div>
    </div>
@endsection
