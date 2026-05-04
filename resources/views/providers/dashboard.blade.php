@extends('layouts.app')

@section('content')
    <h1>Tableau de bord prestataire</h1>
    <p>Bienvenue, {{ auth()->user()->getFullName() }} !</p>
    <!-- Ajoute ici le contenu spécifique aux prestataires -->
    <a href="{{ route('user.profile') }}">Gérer mon profil</a>
@endsection
