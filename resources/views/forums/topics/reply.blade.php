@extends('layouts.layout')

@section('content')
    <div class="main-content">
        <div class="container">
            <h1 class="mb-4">Répondre au sujet : {{ $topic->title }}</h1>

            <form action="{{ route('forums.topics.replies.store', [$topic->forum, $topic]) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="content" class="form-label">Contenu</label>
                    <textarea class="form-control" id="reply-content" name="content" rows="5" required>
@if(isset($reply))
                            > **{{ $reply->user->name }} a écrit :**
                            {!! str_replace("\n", "\n> ", e($reply->content)) !!}

                        @endif
                    </textarea>
                    <input type="hidden" name="quote_id" value="{{ $reply->id ?? null }}">
                    <small class="form-text text-muted">
                        Vous pouvez utiliser le <a href="https://www.markdownguide.org/basic-syntax/" target="_blank">Markdown</a> pour formater votre texte.
                    </small>
                </div>
                <button type="submit" class="btn btn-retro">Répondre</button>
            </form>

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
                        forceSync: true
                    });

                    // Positionner le curseur à la fin du contenu
                    var codemirror = editor.codemirror;
                    var lastLine = codemirror.lineCount() - 1;
                    var lastChar = codemirror.getLine(lastLine).length;
                    codemirror.setCursor(lastLine, lastChar);
                    codemirror.focus();
                });
            </script>
        </div>
    </div>
@endsection
