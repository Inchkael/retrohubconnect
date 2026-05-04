<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content login-modal">
        <div class="modal-header border-0">
            <h5 class="modal-title" id="forgotPasswordModalLabel">Réinitialiser votre mot de passe</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
            <div class="text-center mb-4">
                <p>Entrez votre adresse email pour recevoir un lien de réinitialisation.</p>
            </div>
            <form id="forgotPasswordForm">
                <div class="mb-3">
                    <input type="text" class="form-control" name="email_or_phone"
                           placeholder="Email" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Envoyer le lien</button>
            </form>
            <div class="text-center mt-3">
                <small class="text-muted">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">
                        Retour à la connexion
                    </a>
                </small>
            </div>
        </div>
    </div>
</div>
</div>
