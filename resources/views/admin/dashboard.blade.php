@extends('layouts.admin')

@section('content')
    <h1>Tableau de bord administration</h1>

    <!-- Boutons de navigation (optionnel) -->
    <div class="row mb-4">
        <div class="col-md-4">
            <a href="{{ route('admin.service_categories.index') }}" class="btn btn-primary btn-lg">
                Gérer les catégories de services
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.sliders.index') }}" class="btn btn-primary btn-lg">
                Gérer le slider
            </a>
        </div>
    </div>

    <!-- Inclusion directe de la vue des catégories de services -->
    @include('admin.service_categories.index')
@endsection
