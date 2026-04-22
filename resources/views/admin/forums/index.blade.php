@extends('layouts.layout')

@section('content')
    <div class="main-content">
        <div class="container">
            <h1 class="mb-4">Gestion des forums</h1>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('admin.forums.create') }}" class="btn btn-retro">
                    <i class="fas fa-plus me-1"></i> {{ __('Créer un nouveau forum') }}
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($forums as $forum)
                        <tr>
                            <td>{{ $forum->name }}</td>
                            <td>{{ $forum->category->name ?? 'Aucune catégorie' }}</td>
                            <td>{{ Str::limit($forum->description, 50) }}</td>
                            <td>
                                <a href="{{ route('admin.forums.edit', $forum) }}" class="btn btn-sm btn-retro">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.forums.destroy', $forum) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce forum ?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
