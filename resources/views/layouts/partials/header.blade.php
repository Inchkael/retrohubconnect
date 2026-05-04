<header class="retro-header">
    <div class="container">
        <div class="header-flex-wrapper">
            <div class="header-logo-section">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('images/logo-retrohubconnect.png') }}" alt="RetroHubConnect" class="retro-logo">
                </a>
            </div>

            <div class="header-search-section">
                <form action="{{ route('search') }}" method="GET" class="input-group">
                    <input type="text" class="form-control retro-search-input" name="query" value="{{ request('query') }}" placeholder="{{ __('messages.search_placeholder') }}">
                    <button class="btn btn-retro" type="submit"><i class="bi bi-search"></i></button>
                </form>
            </div>

            <div class="header-actions-section">
                <div class="user-actions">
                    @auth
                        @php $unreadCount = Auth::user()->unreadMessages()->count(); @endphp
                        <a class="nav-link-header {{ request()->routeIs('messages.*') ? 'active' : '' }}" href="{{ route('messages.inbox') }}">
                            <span>MESSAGES</span>
                            @if($unreadCount > 0) <span class="badge rounded-pill bg-danger">{{ $unreadCount }}</span> @endif
                        </a>
                        <a class="nav-link-header {{ request()->routeIs('user.profile') ? 'active' : '' }}" href="{{ route('user.profile') }}">
                            <span>MON PROFIL</span>
                        </a>
                        <a class="nav-link-header logout-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <span>DÉCONNEXION</span>
                        </a>
                        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="d-none">@csrf</form>
                    @else
                        <a class="nav-link-header" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">CONNEXION</a>
                    @endauth
                </div>

                <div class="language-selector">
                    <button class="language-selector__button">
                        <img src="/images/{{ app()->getLocale() }}.jpg" class="language-selector__flag">
                        <span class="language-selector__text">{{ strtoupper(app()->getLocale()) }}</span>
                        <i class="bi bi-chevron-down ms-1" style="font-size: 10px;"></i>
                    </button>
                    <ul class="language-selector__dropdown">
                        <li><a href="{{ route('set.locale', ['locale' => 'fr']) }}" class="language-selector__link"><img src="/images/fr.jpg" class="language-selector__flag"> FR</a></li>
                        <li><a href="{{ route('set.locale', ['locale' => 'en']) }}" class="language-selector__link"><img src="/images/en.jpg" class="language-selector__flag"> EN</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<style>
    .retro-header { background: var(--primary-color); padding: 10px 0; z-index: 1050; position: relative; }

    /* Utilisation de flex-wrap pour la fluidité */
    .header-flex-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        flex-wrap: wrap; /* */
    }

    .header-logo-section { flex-shrink: 0; order: 1; }
    .retro-logo { max-height: 45px; transition: transform 0.3s; }
    .retro-logo:hover { transform: scale(1.05); }

    /* Modification majeure : La recherche passe en bas plus tôt */
    .header-search-section {
        flex-grow: 1;
        max-width: 400px;
        order: 2;
    }

    .retro-search-input { border-radius: 4px 0 0 4px; border: none; }
    .btn-retro { background: var(--secondary-color); color: white; border-radius: 0 4px 4px 0; }

    .header-actions-section {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
        order: 3;
    }
    .user-actions { display: flex; align-items: center; }

    .nav-link-header {
        color: white !important; font-size: 0.75rem; font-weight: bold; text-decoration: none;
        padding: 5px 15px; position: relative; transition: color 0.3s ease;
    }

    .nav-link-header::after {
        content: ''; position: absolute; width: 0; height: 2px; bottom: -2px; left: 15px;
        background-color: var(--secondary-color); transition: width 0.3s ease;
    }
    .nav-link-header:hover::after, .nav-link-header.active::after { width: calc(100% - 30px); }
    .nav-link-header:hover, .nav-link-header.active { color: var(--secondary-color) !important; }

    @media (min-width: 992px) {
        .nav-link-header:not(:last-child) { border-right: 1px solid rgba(255,255,255,0.2); }
    }

    /* Sélecteur de langue compact */
    .language-selector { position: relative; }
    .language-selector__button { background: none; border: none; color: white; display: flex; align-items: center; padding: 5px 10px; cursor: pointer; }
    .language-selector__flag { width: 18px; margin-right: 5px; }
    .language-selector__dropdown {
        position: absolute; top: 100%; left: 50%; transform: translateX(-50%) translateY(10px);
        width: 80px; background: white; border-radius: 4px; display: none; list-style: none; padding: 5px 0; box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    .language-selector:hover .language-selector__dropdown { display: block; transform: translateX(-50%) translateY(0); }
    .language-selector__link { display: flex; align-items: center; justify-content: center; padding: 8px; color: #333; text-decoration: none; font-size: 12px; }

    /* REGLAGE DU POINT DE RUPTURE (MOBILE & TABLETTE) */
    @media (max-width: 991px) {
        .header-logo-section { order: 1; }
        .header-actions-section { order: 2; }
        .header-search-section {
            order: 3;
            width: 100%;
            max-width: 100%;
            margin-top: 5px;
        }
        .nav-link-header { padding: 5px 10px; font-size: 0.7rem; }
        .nav-link-header::after { display: none; }
    }

    @media (max-width: 480px) {
        .header-actions-section { gap: 5px; }
        .nav-link-header span { font-size: 0.65rem; }
    }
</style>
