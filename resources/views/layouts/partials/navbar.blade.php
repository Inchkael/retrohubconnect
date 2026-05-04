<nav class="navbar-retro">
    <div class="container d-flex flex-column align-items-center">
        <button id="customToggler" class="hamburger-btn d-lg-none">
            <div class="hamburger-icon">
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
            </div>
            <span class="ms-2 fw-bold">MENU</span>
        </button>

        <div id="customMenu" class="nav-wrapper-mobile">
            <ul class="nav-list-custom">
                <li><a href="{{ route('home') }}" class="nav-link-custom {{ request()->routeIs('home') ? 'active' : '' }}">ACCUEIL</a></li>
                <li><a href="{{ route('marketplace.items.index') }}" class="nav-link-custom {{ request()->routeIs('marketplace.*') ? 'active' : '' }}">MARKETPLACE</a></li>
                <li><a href="{{ route('forums.index') }}" class="nav-link-custom {{ request()->routeIs('forums.*') ? 'active' : '' }}">FORUMS</a></li>
                <li><a href="{{ route('about') }}" class="nav-link-custom {{ request()->routeIs('about') ? 'active' : '' }}">À PROPOS</a></li>
                <li><a href="{{ route('contact') }}" class="nav-link-custom {{ request()->routeIs('contact') ? 'active' : '' }}">CONTACT</a></li>
            </ul>
        </div>
    </div>
</nav>

<style>
    .navbar-retro { background: var(--tertiary-color); padding: 10px 0; border-top: 1px solid rgba(255,255,255,0.1); width: 100%; position: relative; }
    .nav-list-custom { display: flex; list-style: none; padding: 0; margin: 0; gap: 10px; }

    .nav-link-custom {
        color: white !important; font-weight: bold; text-decoration: none; font-size: 0.9rem;
        position: relative; padding: 5px 20px; transition: color 0.3s ease;
    }

    /* Animation Barre Orange Navbar */
    .nav-link-custom::after {
        content: ''; position: absolute; width: 0; height: 2px; bottom: -5px; left: 20px;
        background-color: var(--secondary-color); transition: width 0.3s ease;
    }
    .nav-link-custom:hover::after, .nav-link-custom.active::after { width: calc(100% - 40px); }
    .nav-link-custom:hover, .nav-link-custom.active { color: var(--secondary-color) !important; }

    /* Séparateurs Desktop */
    @media (min-width: 992px) {
        .nav-list-custom li:not(:last-child) .nav-link-custom { border-right: 1px solid rgba(255,255,255,0.15); }
    }

    /* Hamburger & Animation Croix */
    .hamburger-btn { background: none; border: 1px solid rgba(255,255,255,0.3); color: white; padding: 8px 15px; display: flex; align-items: center; border-radius: 4px; cursor: pointer; }
    .hamburger-icon { width: 20px; display: flex; flex-direction: column; gap: 4px; }
    .bar { width: 100%; height: 2px; background: white; transition: all 0.3s ease; }

    .hamburger-btn.active .bar:nth-child(1) { transform: translateY(6px) rotate(45deg); }
    .hamburger-btn.active .bar:nth-child(2) { opacity: 0; }
    .hamburger-btn.active .bar:nth-child(3) { transform: translateY(-6px) rotate(-45deg); }

    /* Menu Mobile Opaque */
    @media (max-width: 991px) {
        .nav-wrapper-mobile {
            display: none; width: 100%; background: var(--tertiary-color);
            position: absolute; top: 100%; left: 0; z-index: 1000; padding: 20px 0;
            border-bottom: 3px solid var(--secondary-color);
        }
        .nav-wrapper-mobile.show { display: block; }
        .nav-list-custom { flex-direction: column; align-items: center; gap: 15px; }
        .nav-link-custom::after { display: none; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('customToggler');
        const menu = document.getElementById('customMenu');
        if(btn && menu) {
            btn.onclick = function(e) {
                e.stopPropagation();
                this.classList.toggle('active');
                menu.classList.toggle('show');
            };
            document.addEventListener('click', function(e) {
                if(!menu.contains(e.target) && !btn.contains(e.target)) {
                    btn.classList.remove('active');
                    menu.classList.remove('show');
                }
            });
        }
    });
</script>
