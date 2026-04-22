@extends('layouts.layout')

@section('content')
    <div class="main-content">
        <div class="container">
            <h1 class="mb-4">Modifier la catégorie de forum</h1>

            <form action="{{ route('admin.forum_categories.update', $forumCategory) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $forumCategory->name }}" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ $forumCategory->description }}</textarea>
                </div>
                <button type="submit" class="btn btn-retro">Mettre à jour</button>
            </form>
        </div>
    </div>
@endsection
