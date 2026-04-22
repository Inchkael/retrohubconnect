@extends('layouts.layout')

@section('content')
    <div class="main-content">
        <div class="container">
            <h1 class="mb-4">Proposer un nouveau forum dans {{ $category->name }}</h1>

            <form action="{{ route('forums.store') }}" method="POST">
                @csrf
                <input type="hidden" name="category_id" value="{{ $category->id }}">
                <div class="mb-3">
                    <label for="name" class="form-label">Nom du forum</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-retro">Proposer</button>
            </form>
        </div>
    </div>
@endsection
