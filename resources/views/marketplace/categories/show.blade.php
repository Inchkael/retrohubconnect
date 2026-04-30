@extends('layouts.layout')

@section('content')
    <h1>{{ $category->name }}</h1>
    <p>{{ $category->description }}</p>
    <a href="{{ route('marketplace.categories.edit', $category) }}" class="btn btn-warning">Modifier</a>
    <form action="{{ route('marketplace.categories.destroy', $category) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr ?')">Supprimer</button>
    </form>
@endsection
