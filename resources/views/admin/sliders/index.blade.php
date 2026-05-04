@extends('layouts.layout')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Gestion des sliders</h1>
            <div>
                <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary me-2">
                    <i class="bi bi-plus-circle me-1"></i> Ajouter
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Liste des sliders</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-light">
                        <tr>
                            <th>Titre</th>
                            <th>Image</th>
                            <th>Statut</th>
                            <th>Date de création</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($sliders->sortBy('position') as $slider)
                            <tr>
                                <td>{{ $slider->title }}</td>
                                <td>
                                    @if($slider->mainImage())
                                        <img src="{{ $slider->mainImage()->url }}"
                                             alt="{{ $slider->title }}"
                                             class="img-thumbnail"
                                             style="width: 80px; height: 60px; object-fit: cover;">
                                    @else
                                        <span class="badge bg-secondary">Aucune image</span>
                                    @endif
                                </td>
                                <td>
                                        <span class="badge {{ $slider->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $slider->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                </td>
                                <td>{{ $slider->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.sliders.edit', $slider) }}" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ route('admin.sliders.destroy', $slider) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $sliders->links() }}
            </div>
        </div>
    </div>

@endsection
