@extends('layouts.layout')

@section('content')
    <div class="main-content">
        <div class="container">
            <!-- Titre principal -->
            <h1 class="text-center mb-5" style="color: var(--dark-color); font-weight: 700;">
                {{ __('Forum RetroHubConnect') }}
            </h1>

            <!-- Description du forum -->
            <div class="text-center mb-5">
                <p class="lead" style="color: var(--text-color); max-width: 800px; margin: 0 auto;">
                    {{ __('Bienvenue sur le forum dédié aux passionnés de rétrogaming ! Posez vos questions, partagez vos découvertes, et discutez avec la communauté.') }}
                </p>
            </div>

            <!-- Barre de recherche -->
            <div class="row justify-content-center mb-4">
                <div class="col-md-6">
                    <form action="{{ route('forums.search') }}" method="GET" class="d-flex">
                        <input type="text" name="query" class="form-control retro-search-input me-2"
                               placeholder="{{ __('Rechercher un forum, un sujet...') }}" aria-label="Rechercher">
                        <button type="submit" class="btn btn-retro">
                            <i class="fas fa-search"></i> {{ __('Rechercher') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Bouton de gestion des catégories pour les administrateurs -->
            @auth
                @if(Auth::user()->isAdmin())
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('admin.forum_categories.index') }}" class="btn btn-retro me-2">
                            <i class="fas fa-cog me-1"></i> {{ __('Gérer les catégories') }}
                        </a>
                        <a href="{{ route('admin.forums.index') }}" class="btn btn-retro">
                            <i class="fas fa-comments me-1"></i> {{ __('Gérer les forums') }}
                        </a>
                    </div>
                @endif
            @endauth



            <!-- Liste des catégories de forums -->
            @forelse($categories as $category)
                <div class="mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="mb-0" style="color: var(--dark-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">
                                {{ $category->name }}
                            </h2>
                        </div>
                    </div>
                    <p class="mb-4" style="color: var(--text-color);">{{ $category->description }}</p>

                    <div class="row g-4">
                        @forelse($category->forums as $forum)
                            <div class="col-md-6 col-lg-4">
                                <div class="service-card forum-card h-100">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h3 class="mb-0" style="color: var(--primary-color); font-weight: 600;">
                                            {{ $forum->name }}
                                        </h3>
                                        <span class="badge">
                                            {{ $forum->topics_count }} {{ __('sujet(s)') }}
                                        </span>
                                    </div>
                                    <p class="mb-3" style="color: var(--text-color);">
                                        {{ $forum->description }}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="{{ route('forums.show', $forum) }}" class="btn btn-retro">
                                            <i class="fas fa-eye me-1"></i> {{ __('Voir les sujets') }}
                                        </a>
                                        @auth
                                            <a href="{{ route('forums.topics.create', ['forum' => $forum->id]) }}" class="btn btn-retro">
                                                <i class="fas fa-plus me-1"></i> {{ __('Nouveau sujet') }}
                                            </a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info">
                                    {{ __('Aucun forum disponible dans cette catégorie.') }}
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <div class="alert alert-info">
                        {{ __('Aucune catégorie de forum disponible pour le moment.') }}
                    </div>
                </div>
            @endforelse

            <!-- Section des sujets récents -->
            @if(isset($recentTopics) && $recentTopics->isNotEmpty())
                <div class="mt-5">
                    <h2 class="mb-4" style="color: var(--dark-color);">{{ __('Sujets récents') }}</h2>
                    <div class="row g-3">
                        @foreach($recentTopics as $topic)
                            <div class="col-12">
                                <div class="topic-card">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h4 class="mb-0" style="color: var(--primary-color);">
                                            <a href="{{ route('forums.topics.show', [$topic->forum, $topic]) }}" style="color: inherit; text-decoration: none;">
                                                {{ $topic->title }}
                                            </a>
                                        </h4>
                                        <span class="badge">
                                            {{ $topic->forum->name }}
                                        </span>
                                    </div>
                                    <p class="mb-3" style="color: var(--text-color);">
                                        {!! $parsedown->text(Str::limit($topic->content, 200)) !!}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge me-2">
                                                <i class="fas fa-user me-1"></i> {{ $topic->user->name ?? 'Utilisateur inconnu' }}
                                            </span>
                                            <span class="badge">
                                                <i class="fas fa-clock me-1"></i>
                                                @if($topic->created_at)
                                                    {{ $topic->created_at->diffForHumans() }}
                                                @else
                                                    Date inconnue
                                                @endif
                                            </span>
                                        </div>
                                        <div>
                                            <span class="badge me-2">
                                                <i class="fas fa-comments me-1"></i> {{ $topic->replies_count }} {{ __('réponse(s)') }}
                                            </span>
                                            <a href="{{ route('forums.topics.show', [$topic->forum, $topic]) }}" class="btn btn-retro btn-sm">
                                                <i class="fas fa-reply me-1"></i> {{ __('Répondre') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
