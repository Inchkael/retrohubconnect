<!-- resources/views/admin/service_categories/edit.blade.php -->
@extends('layouts.layout')

@section('title', 'Modifier une catégorie de service')

@section('content')
    <div class="container">
        <h1>Modifier une catégorie de service</h1>

        <form action="{{ route('admin.service_categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Nom</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $category->name }}" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3">{{ $category->description }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>
@endsection
