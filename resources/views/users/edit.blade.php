@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Modifier l'utilisateur</h1>
        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="last_name">Nom</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $user->last_name }}" required>
            </div>
            <div class="form-group">
                <label for="first_name">Prénom</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $user->first_name }}" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
            </div>
            <div class="form-group">
                <label for="password">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="form-group">
                <label for="role">Rôle</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="visitor" {{ $user->role === 'visitor' ? 'selected' : '' }}>Visiteur</option>
                    <option value="member" {{ $user->role === 'member' ? 'selected' : '' }}>Membre</option>
                    <option value="seller" {{ $user->role === 'seller' ? 'selected' : '' }}>Vendeur</option>
                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrateur</option>
                </select>
            </div>
            <div class="form-group">
                <label for="language">Langue</label>
                <select class="form-control" id="language" name="language" required>
                    <option value="fr" {{ $user->language === 'fr' ? 'selected' : '' }}>Français</option>
                    <option value="en" {{ $user->language === 'en' ? 'selected' : '' }}>Anglais</option>
                    <option value="de" {{ $user->language === 'de' ? 'selected' : '' }}>Allemand</option>
                    <option value="nl" {{ $user->language === 'nl' ? 'selected' : '' }}>Néerlandais</option>
                </select>
            </div>
            <div class="form-group">
                <label for="enabled">Actif</label>
                <select class="form-control" id="enabled" name="enabled" required>
                    <option value="1" {{ $user->enabled ? 'selected' : '' }}>Oui</option>
                    <option value="0" {{ !$user->enabled ? 'selected' : '' }}>Non</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>
@endsection
