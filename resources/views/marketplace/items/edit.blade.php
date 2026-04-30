{{--
/**
 * Fichier: resources/views/marketplace/edit.blade.php
--}}

@extends('layouts.layout')

@section('content')
    <div class="container" style="padding-top: 2rem; max-width: 800px; margin: 0 auto;">
        <h1 style="color: var(--dark-color); margin-bottom: 1.5rem;">Modifier l'article</h1>

        <div class="service-card p-4">
            <form method="POST" action="{{ route('marketplace.items.update', $item) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="title" class="form-label" style="color: var(--dark-color);">Titre</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $item->title) }}" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label" style="color: var(--dark-color);">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4" required>{{ old('description', $item->description) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label" style="color: var(--dark-color);">Prix (€)</label>
                    <input type="number" class="form-control" id="price" name="price" step="0.01" value="{{ old('price', $item->price) }}" required>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label" style="color: var(--dark-color);">Catégorie</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $item->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="condition" class="form-label" style="color: var(--dark-color);">État</label>
                    <select class="form-select" id="condition" name="condition" required>
                        <option value="new" {{ $item->condition == 'new' ? 'selected' : '' }}>Neuf</option>
                        <option value="used" {{ $item->condition == 'used' ? 'selected' : '' }}>Occasion</option>
                        <option value="collector" {{ $item->condition == 'collector' ? 'selected' : '' }}>Collection</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="location" class="form-label" style="color: var(--dark-color);">Localisation (optionnel)</label>
                    <input type="text" class="form-control" id="location" name="location" value="{{ old('location', $item->location) }}">
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ $item->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active" style="color: var(--dark-color);">
                        Article actif
                    </label>
                </div>

                <div class="mb-3">
                    <label for="images" class="form-label" style="color: var(--dark-color);">Ajouter des images (optionnel)</label>
                    <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                    <small class="text-muted">Vous pouvez ajouter plusieurs images.</small>
                </div>

                <button type="submit" class="btn-retro">
                    <i class="bi bi-save me-2"></i>Mettre à jour
                </button>
            </form>
        </div>
    </div>
@endsection
