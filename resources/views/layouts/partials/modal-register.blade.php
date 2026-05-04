<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content login-modal">
        <div class="modal-header border-0">
            <h5 class="modal-title" id="registerModalLabel">Créer un compte</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
            <form id="registerForm" method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-3">
                    <input type="text" class="form-control mb-2" name="first_name" placeholder="Prénom" required>
                    <input type="text" class="form-control mb-2" name="last_name" placeholder="Nom" required>
                    <input type="email" class="form-control mb-2" name="email" placeholder="Adresse e-mail" required>
                    <input type="password" class="form-control mb-2" name="password" placeholder="Mot de passe" required>
                    <input type="password" class="form-control" name="password_confirmation" placeholder="Confirmer le mot de passe" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
            </form>
        </div>
    </div>
</div>
