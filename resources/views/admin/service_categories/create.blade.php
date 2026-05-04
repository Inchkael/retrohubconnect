<!-- resources/views/admin/service_categories/create.blade.php -->
@extends('layouts.layout')

@section('title', 'Ajouter une catégorie de service')

@section('content')
    <div class="container">
        <h1>Ajouter une catégorie de service</h1>

        <form action="{{ route('admin.service_categories.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Nom</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
    </div>
@endsection
