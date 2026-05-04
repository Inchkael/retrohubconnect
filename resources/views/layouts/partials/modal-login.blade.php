<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content login-modal">
        <div class="modal-header border-0">
            <h5 class="modal-title" id="loginModalLabel">Identifiez-vous ou créez un compte</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
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
                <button type="submit" class="btn btn-primary w-100 mb-3">Se connecter</button>
                <div class="divider"></div>
                <button type="button" class="btn btn-social d-flex align-items-center justify-content-center"
                        onclick="window.location.href='{{ route('google.login') }}'">
                    <i class="bi bi-google me-2" style="color: #4285F4;"></i>
                    Continuer avec Google
                </button>
            </form>
            <div class="text-center mt-3">
                <small class="text-muted">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal">Créer un compte</a>
                </small>
            </div>
            <!-- Lien pour la réinitialisation du mot de passe -->
            <div class="text-center mt-2">
                <small class="text-muted">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal" data-bs-dismiss="modal">
                        Mot de passe oublié ?
                    </a>
                </small>
            </div>

        </div>
        <div class="modal-footer border-0 justify-content-center">
            <small class="text-muted">
                En continuant, vous acceptez nos <a href="#">Conditions d'utilisation</a> et notre <a href="#">Politique de confidentialité</a>
            </small>
        </div>
    </div>
</div>
