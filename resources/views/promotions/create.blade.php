@extends('layouts.layout')

@section('title', 'Ajouter une promotion')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Ajouter une promotion</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('promotions.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="Nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="Nom" name="Nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="Description" class="form-label">Description</label>
                        <textarea class="form-control" id="Description" name="Description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="CategorieDeServicesID" class="form-label">Catégorie de service</label>
                        <select class="form-select" id="CategorieDeServicesID" name="CategorieDeServicesID" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="Début" class="form-label">Date de début</label>
                            <input type="date" class="form-control" id="Début" name="Début" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="Fin" class="form-label">Date de fin</label>
                            <input type="date" class="form-control" id="Fin" name="Fin" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="AffichageDébut" class="form-label">Date d'affichage début</label>
                            <input type="date" class="form-control" id="AffichageDébut" name="AffichageDébut" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="AffichageFin" class="form-label">Date d'affichage fin</label>
                            <input type="date" class="form-control" id="AffichageFin" name="AffichageFin" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="pdf" class="form-label">Document PDF</label>
                        <input type="file" class="form-control" id="pdf" name="pdf" accept=".pdf" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </form>
            </div>
        </div>
    </div>
@endsection
