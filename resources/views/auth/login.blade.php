@extends('layouts.layout')

@section('title', 'Connexion')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm login-modal">
                    <div class="card-header bg-white border-0">
                        <h5 class="modal-title text-center" id="loginModalLabel">Identifiez-vous ou créez un compte</h5>
                    </div>
                    <div class="card-body">
                        <!-- Zone pour afficher les messages d'erreur et d'information -->
                        <div id="login-message" class="mb-3"></div>

                        <div class="text-center mb-4">
                            <p>Saisissez votre adresse e-mail</p>
                        </div>
                        <form id="loginForm" method="POST" action="{{ route('login.submit') }}">
                            @csrf
                            <div class="mb-3">
                                <input type="email" class="form-control mb-2 @error('email') is-invalid @enderror"
                                       name="email" placeholder="Email" value="{{ old('email') }}" required autofocus>
                                @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       name="password" placeholder="Mot de passe" required>
                                @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3" id="loginSubmitBtn">Se connecter</button>
                            <div class="divider"></div>
                            <button type="button" class="btn btn-social d-flex align-items-center justify-content-center"
                                    onclick="window.location.href='{{ route('google.login') }}'">
                                <i class="bi bi-google me-2" style="color: #4285F4;"></i>
                                Continuer avec Google
                            </button>
                        </form>
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <a href="{{ route('register') }}" class="text-decoration-none">Créer un compte</a>
                            </small>
                        </div>

                    </div>
                    <div class="card-footer bg-white border-0 text-center">
                        <small class="text-muted">
                            En continuant, vous acceptez nos <a href="#">Conditions d'utilisation</a> et notre <a href="#">Politique de confidentialité</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Style CSS pour le divider et les boutons sociaux -->
    <style>
        .login-modal {
            backdrop-filter: blur(12px);
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            max-width: 500px;
            margin: 1.75rem auto;
        }

        .divider {
            text-align: center;
            margin: 15px 0;
            color: #6c757d;
            position: relative;
        }

        .divider::before {
            content: "ou";
            display: inline-block;
            background: white;
            padding: 0 10px;
        }

        .divider::after {
            content: "";
            display: block;
            height: 1px;
            background: rgba(0, 0, 0, 0.1);
            margin-top: 8px;
            position: absolute;
            width: 100%;
            top: 50%;
            z-index: -1;
        }

        .btn-social {
            width: 100%;
            margin-bottom: 10px;
            background-color: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .btn-social:hover {
            background-color: rgba(255, 255, 255, 0.9);
            transform: translateY(-2px);
        }

        /* Styles pour les messages d'erreur */
        .login-error {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 0.25rem;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        .attempts-warning {
            font-weight: bold;
            color: #dc3545;
        }

        .locked-message {
            color: #dc3545;
            font-weight: bold;
            font-size: 1.1em;
        }
    </style>
@endsection

<!-- Script pour gérer la connexion et la redirection -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginForm = document.getElementById('loginForm');
        const loginMessage = document.getElementById('login-message');
        const submitButton = document.getElementById('loginSubmitBtn');

        if (loginForm) {
            loginForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Connexion en cours...
                `;

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
                        // Vérifie si l'utilisateur doit finaliser son inscription
                        if (data.needs_finalization) {
                            window.location.href = `/complete-registration?token=${data.token}`;
                        } else {
                            await Swal.fire({
                                icon: 'success',
                                title: 'Connexion réussie !',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            window.location.href = data.redirect;
                        }
                    } else {
                        // Affiche les erreurs de validation
                        let message = data.message;

                        // Si le message contient des informations sur les tentatives restantes
                        if (data.remaining_attempts !== undefined) {
                            if (data.remaining_attempts > 0) {
                                // Mettre en valeur le nombre de tentatives restantes
                                message = message.replace(
                                    /(\d+)/,
                                    (match) => `<span class="attempts-warning">${match}</span>`
                                );
                            } else {
                                // Si le compte est bloqué, on met en valeur tout le message
                                message = `<span class="locked-message">${message}</span>`;
                            }
                        }

                        // Afficher le message dans la div dédiée
                        loginMessage.innerHTML = `<div class="login-error">${message}</div>`;

                        // Désactiver le bouton si le compte est bloqué
                        if (message.includes('bloqué') || message.includes('locked')) {
                            submitButton.disabled = true;
                            submitButton.textContent = 'Compte bloqué';
                        }
                    }
                } catch (error) {
                    console.error('Erreur complète:', error);
                    loginMessage.innerHTML = `
                        <div class="login-error">
                            Une erreur technique est survenue. Veuillez réessayer.
                        </div>
                    `;
                } finally {
                    if (!submitButton.disabled) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = 'Se connecter';
                    }
                }
            });
        }
    });
</script>
