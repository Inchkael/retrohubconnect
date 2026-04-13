@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Détails de l'utilisateur</h1>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ $user->last_name }} {{ $user->first_name }}</h5>
                <p class="card-text"><strong>Email:</strong> {{ $user->email }}</p>
                <p class="card-text"><strong>Rôle:</strong> {{ $user->role }}</p>
                <p class="card-text"><strong>Langue:</strong> {{ $user->language }}</p>
                <p class="card-text"><strong>Actif:</strong> {{ $user->enabled ? 'Oui' : 'Non' }}</p>
                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">Modifier</a>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Retour</a>
            </div>
        </div>
    </div>
@endsection
