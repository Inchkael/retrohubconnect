@extends('layouts.layout')

@section('content')
    <div class="container" style="padding-top: 2rem; max-width: 800px; margin: 0 auto;">
        <h1 style="color: var(--dark-color); margin-bottom: 1.5rem;">Créer un nouvel article</h1>

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Erreur(s) :</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="service-card p-4">
            <form method="POST" action="{{ route('marketplace.items.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="title" class="form-label" style="color: var(--dark-color);">Titre</label>
                    <input type="text" class="form-control" id="title" name="title"
                           value="{{ old('title') }}" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label" style="color: var(--dark-color);">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label" style="color: var(--dark-color);">Prix (€)</label>
                    <input type="number" class="form-control" id="price" name="price"
                           step="0.01" min="0" value="{{ old('price') }}" required>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label" style="color: var(--dark-color);">Catégorie</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="" selected disabled>Choisir une catégorie</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="condition" class="form-label" style="color: var(--dark-color);">État</label>
                    <select class="form-select" id="condition" name="condition" required>
                        <option value="new" {{ old('condition') == 'new' ? 'selected' : '' }}>Neuf</option>
                        <option value="used" {{ old('condition') == 'used' ? 'selected' : '' }}>Occasion</option>
                        <option value="collector" {{ old('condition') == 'collector' ? 'selected' : '' }}>Collection</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="location" class="form-label" style="color: var(--dark-color);">Localisation (optionnel)</label>
                    <input type="text" class="form-control" id="location" name="location"
                           value="{{ old('location') }}">
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                           {{ old('is_active', true) ? 'checked' : '' }} value="1">
                    <label class="form-check-label" for="is_active" style="color: var(--dark-color);">
                        Article actif
                    </label>
                </div>

                <div class="mb-3">
                    <label for="images" class="form-label" style="color: var(--dark-color);">Images (optionnel)</label>
                    <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                    <small class="text-muted">Vous pouvez télécharger plusieurs images (max 5Mo chacune).</small>
                </div>

                <button type="submit" class="btn-retro">
                    <i class="bi bi-save me-2"></i> Publier l'article
                </button>
            </form>
        </div>
    </div>
@endsection
