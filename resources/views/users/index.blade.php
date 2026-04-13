@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Liste des utilisateurs</h1>
        <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">Ajouter un utilisateur</a>
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Actif</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->last_name }}</td>
                    <td>{{ $user->first_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>{{ $user->enabled ? 'Oui' : 'Non' }}</td>
                    <td>
                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-info">Voir</a>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">Modifier</a>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
