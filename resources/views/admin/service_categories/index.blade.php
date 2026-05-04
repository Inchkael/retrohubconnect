<!-- resources/views/admin/service_categories/index.blade.php -->
@extends('layouts.layout')

@section('title', 'Gestion des catégories de services')

@section('content')
    <div class="container">
        <h1>Gestion des catégories de services</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <a href="{{ route('admin.service_categories.create') }}" class="btn btn-primary mb-3">Ajouter une catégorie</a>

        <table class="table table-striped">
            <thead>
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Validée</th>
                <th>Catégorie du mois</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($categories as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->description }}</td>
                    <td>
                        @if($category->is_validated)
                            <span class="badge bg-success">Validée</span>
                        @else
                            <span class="badge bg-danger">Non validée</span>
                        @endif
                    </td>
                    <td>
                        @if($category->is_monthly)
                            <span class="badge bg-primary">Catégorie du mois</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.service_categories.edit', $category->id) }}" class="btn btn-sm btn-warning">Modifier</a>
                        <form action="{{ route('admin.service_categories.destroy', $category->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">Supprimer</button>
                        </form>
                        @unless($category->is_validated)
                            <form action="{{ route('admin.service_categories.validate', $category->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Valider</button>
                            </form>
                        @endunless
                        @unless($category->is_monthly)
                            <form action="{{ route('admin.service_categories.set_as_monthly', $category->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-info">Catégorie du mois</button>
                            </form>
                        @endunless
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#transferModal-{{ $category->id }}">Transférer</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modale de transfert -->
    @foreach($categories as $category)
        <div class="modal fade" id="transferModal-{{ $category->id }}" tabindex="-1" aria-labelledby="transferModalLabel-{{ $category->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="transferModalLabel-{{ $category->id }}">Transférer les prestataires</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <form action="{{ route('admin.service_categories.transfer_providers', $category->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="new_category_id" class="form-label">Nouvelle catégorie</label>
                                <select class="form-select" id="new_category_id" name="new_category_id" required>
                                    @foreach($categories->where('id', '!=', $category->id) as $newCategory)
                                        <option value="{{ $newCategory->id }}">{{ $newCategory->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary">Transférer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
