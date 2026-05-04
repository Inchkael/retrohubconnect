@extends('layouts.layout')

@section('title', 'Complétez votre inscription')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Complétez votre inscription</h4>
                    </div>
                    <div class="card-body">
                        <form id="completeRegistrationForm" method="POST" action="{{ route('complete.registration') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">Prénom</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name"
                                           value="{{ old('first_name', $user->first_name ?? '') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Nom</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name"
                                           value="{{ old('last_name', $user->last_name ?? '') }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Adresse</label>
                                <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="mobile_phone" class="form-label">Téléphone</label>
                                <input type="text" class="form-control" id="mobile_phone" name="mobile_phone" value="{{ old('mobile_phone') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="vat_number" class="form-label">Numéro de TVA (optionnel)</label>
                                <input type="text" class="form-control" id="vat_number" name="vat_number" value="{{ old('vat_number') }}">
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Rôle</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="USER" {{ old('role') === 'USER' ? 'selected' : '' }}>Utilisateur</option>
                                    <option value="PROVIDER" {{ old('role') === 'PROVIDER' ? 'selected' : '' }}>Prestataire</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Compléter l'inscription</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('completeRegistrationForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const submitButton = this.querySelector('button[type="submit"]');

            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> En cours...`;

            try {
                const formData = new FormData(this);
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();

                if (data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Succès',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 2000
                    });
                    window.location.href = data.redirect;
                } else {
                    await Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: data.message,
                    });
                }
            } catch (error) {
                console.error('Erreur:', error);
                await Swal.fire({
                    icon: 'error',
                    title: 'Erreur technique',
                    text: 'Une erreur est survenue. Veuillez réessayer.',
                });
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Compléter l\'inscription';
            }
        });
    </script>
@endsection
