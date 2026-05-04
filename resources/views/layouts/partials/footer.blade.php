<footer class="bg-dark text-white py-3">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <h5 class="mb-2"><i class="bi bi-info-circle me-2"></i> À propos</h5>
                <p class="small mb-0">
                    Créé par Mickael Collings, Espace Bien-Être propose des services de massage, yoga et coaching pour votre équilibre quotidien.
                </p>
            </div>

            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <h5 class="mb-2"><i class="bi bi-link-45deg me-2"></i> Liens rapides</h5>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-1"><a href="{{ route('home') }}" class="text-white">Accueil</a></li>
                    <li class="mb-1"><a href="{{ route('about') }}" class="text-white">À propos</a></li>
                    <li class="mb-1"><a href="{{ route('contact') }}" class="text-white">Contact</a></li>
                </ul>
            </div>

            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <h5 class="mb-2"><i class="bi bi-share me-2"></i> Suivez-nous</h5>
                <div class="d-flex gap-2">
                    <a href="#" class="text-white fs-5" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-white fs-5" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-white fs-5" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>

            <div class="col-md-3 col-6">
                <h5 class="mb-2"><i class="bi bi-shield-check me-2"></i> Légal</h5>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-1"><a href="#" class="text-white-50">Mentions légales</a></li>
                    <li class="mb-1"><a href="#" class="text-white-50">Confidentialité</a></li>
                </ul>
            </div>
        </div>

        <hr class="my-3">
        <div class="text-center small">
            <p class="mb-0">&copy; {{ date('Y') }} Espace de Mickael Collings. Tous droits réservés.</p>
        </div>
    </div>
</footer>
