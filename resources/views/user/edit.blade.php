@extends('layouts.layout')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Éditer l'utilisateur</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('users.update', $user) }}">
                            @csrf
                            @method('PUT')

                            <!-- Champs existants -->
                            <div class="form-group row">
                                <label for="last_name" class="col-md-4 col-form-label text-md-right">Nom</label>
                                <div class="col-md-6">
                                    <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror"
                                           name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                                    @error('last_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>


                            <!-- Champ adresse avec géocodage -->
                            <div class="form-group row">
                                <label for="address" class="col-md-4 col-form-label text-md-right">Adresse</label>
                                <div class="col-md-6">
                                    <input id="address" type="text" class="form-control @error('address') is-invalid @enderror"
                                           name="address" value="{{ old('address', $user->address) }}">
                                    @error('address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror

                                    @if($user->latitude && $user->longitude)
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                Coordonnées géographiques enregistrées:
                                                <strong>Lat: {{ $user->latitude }}, Long: {{ $user->longitude }}</strong>
                                            </small>
                                            <div id="map-preview" style="height: 200px; width: 100%; margin-top: 10px; border: 1px solid #ddd;"></div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Bouton de soumission -->
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Mettre à jour
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialiser une carte de prévisualisation si des coordonnées existent
                @if($user->latitude && $user->longitude)
                const map = L.map('map-preview').setView([{{ $user->latitude }}, {{ $user->longitude }}], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                L.marker([{{ $user->latitude }}, {{ $user->longitude }}]).addTo(map)
                    .bindPopup("{{ $user->address }}")
                    .openPopup();
                @endif
            });
        </script>
    @endpush
@endsection
