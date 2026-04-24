@extends('layouts.layout')

@section('content')
    <div class="main-content">
        <div class="container">
            @if(isset($global) && $global)
                <h1 class="mb-4">Gestion de tous les sujets</h1>
            @elseif(isset($category))
                <h1 class="mb-4">Gestion des sujets - Catégorie : {{ $category->name }}</h1>
            @elseif(isset($forum))
                <h1 class="mb-4">Gestion des sujets - Forum : {{ $forum->name }}</h1>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-3">
                @if(isset($category))
                    <a href="{{ route('admin.forums.topics.global') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Voir tous les sujets
                    </a>
                @elseif(isset($forum))
                    <a href="{{ route('admin.forums.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Retour aux forums
                    </a>
                @endif

                <div class="d-flex gap-2">
                    @if(isset($forum))
                        <a href="{{ route('admin.forums.topics.create', $forum) }}" class="btn btn-retro">
                            <i class="fas fa-plus me-1"></i> Créer un nouveau sujet
                        </a>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <form action="@if(isset($global))
                        {{ route('admin.forums.topics.global') }}
                    @elseif(isset($category))
                        {{ route('admin.forums.topics.by_category', $category->id) }}
                    @elseif(isset($forum))
                        {{ route('admin.forums.topics.index', $forum) }}
                    @endif" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2"
                               placeholder="Rechercher un sujet..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-retro">
                            <i class="fas fa-search me-1"></i> Rechercher
                        </button>
                    </form>
                </div>
            </div>

            @if(isset($category))
                <div class="alert alert-info mb-4">
                    Vous visualisez les sujets de la catégorie "{{ $category->name }}".
                </div>
            @elseif(isset($forum))
                <div class="alert alert-info mb-4">
                    Vous visualisez les sujets du forum "{{ $forum->name }}".
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Titre</th>
                        @if(!isset($forum))
                            <th>Forum</th>
                            <th>Catégorie</th>
                        @endif
                        <th>Auteur</th>
                        <th>Date de création</th>
                        <th>Réponses</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($topics as $topic)
                        <tr>
                            <td>
                                <a href="{{ route('forums.topics.show', [$topic->forum, $topic]) }}" target="_blank">
                                    {{ Str::limit($topic->title, 50) }}
                                </a>
                            </td>
                            @if(!isset($forum))
                                <td>
                                    <a href="{{ route('forums.show', $topic->forum) }}" target="_blank">
                                        {{ $topic->forum->name }}
                                    </a>
                                </td>
                                <td>{{ $topic->forum->category->name ?? 'Aucune' }}</td>
                            @endif
                            <td>
                                @if($topic->user)
                                    {{ $topic->user->first_name ?? '' }} {{ $topic->user->last_name ?? $topic->user->name }}
                                @else
                                    Utilisateur inconnu
                                @endif
                            </td>
                            <td>{{ $topic->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</td>
                            <td>{{ $topic->replies_count }}</td>
                            <td>
                                @if($topic->is_locked)
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-lock me-1"></i> Verrouillé
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="fas fa-lock-open me-1"></i> Ouvert
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.forums.topics.edit', [$topic->forum, $topic]) }}" class="btn btn-sm btn-retro" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    @if($topic->is_locked)
                                        <form action="{{ route('admin.forums.topics.unlock', $topic) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Déverrouiller">
                                                <i class="fas fa-lock-open"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.forums.topics.lock', $topic) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning" title="Verrouiller">
                                                <i class="fas fa-lock"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.forums.topics.destroy', $topic) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce sujet ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="@if(!isset($forum)) 8 @else 7 @endif" class="text-center">
                                <div class="alert alert-info mb-0">
                                    Aucun sujet trouvé.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- CSS pour les badges -->
    <style>
        .badge.bg-success {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
@endsection
