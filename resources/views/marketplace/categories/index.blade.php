@extends('layouts.layout')

@section('content')
    <h1>Catégories</h1>
    <a href="{{ route('marketplace.categories.create') }}" class="btn btn-primary mb-3">Nouvelle catégorie</a>
    <table class="table">
        <thead>
        <tr>
            <th>Nom</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse($categories as $category)
            <tr>
                <td>{{ $category->name }}</td>
                <td>{{ Str::limit($category->description, 50) }}</td>
                <td>
                    <a href="{{ route('marketplace.categories.show', $category) }}" class="btn btn-sm btn-info">Voir</a>
                    <a href="{{ route('marketplace.categories.edit', $category) }}" class="btn btn-sm btn-warning">Modifier</a>
                    <form action="{{ route('marketplace.categories.destroy', $category) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr ?')">Supprimer</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3">Aucune catégorie disponible.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
@endsection
