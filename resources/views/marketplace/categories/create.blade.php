@extends('layouts.layout')

@section('content')
    <h1>Créer une nouvelle catégorie</h1>
    <form method="POST" action="{{ route('marketplace.categories.store') }}">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nom</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Créer</button>
    </form>
@endsection
