@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Ajouter un utilisateur</h1>
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="last_name">Nom</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>
            <div class="form-group">
                <label for="first_name">Prénom</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Rôle</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="visitor">Visiteur</option>
                    <option value="member">Membre</option>
                    <option value="seller">Vendeur</option>
                    <option value="admin">Administrateur</option>
                </select>
            </div>
            <div class="form-group">
                <label for="language">Langue</label>
                <select class="form-control" id="language" name="language" required>
                    <option value="fr">Français</option>
                    <option value="en">Anglais</option>
                    <option value="de">Allemand</option>
                    <option value="nl">Néerlandais</option>
                </select>
            </div>
            <div class="form-group">
                <label for="enabled">Actif</label>
                <select class="form-control" id="enabled" name="enabled" required>
                    <option value="1">Oui</option>
                    <option value="0">Non</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
    </div>
@endsection
