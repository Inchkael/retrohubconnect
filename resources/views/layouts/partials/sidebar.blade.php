<div class="sidebar-container d-none d-md-block">
    <div class="sidebar-toggle" id="sidebarToggle"></div>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header d-flex justify-content-between align-items-center ps-3 pe-3">
            <h2 class="h5 mb-0 d-flex align-items-center">
                <i class="bi bi-tag me-2"></i> Services proposés
            </h2>
            @auth
                @if(auth()->check() && auth()->user()->isAdmin())
                    <a href="{{ route('admin.service_categories.index') }}" class="btn btn-sm btn-outline-success" title="Gérer les catégories">
                        <i class="bi bi-gear-fill"></i>
                    </a>
                @endif
            @endauth
        </div>
        <div class="sidebar-content">
            <ul class="nav flex-column">
                @foreach($categories ?? [] as $category)
                    <li class="nav-item mb-2">
                        <div class="d-flex align-items-center">
                            <a class="nav-link d-flex align-items-center ps-3 {{ Request::is('service_categories/' . ($category->id ?? '')) ? 'active' : '' }}"
                               href="{{ route('service_categories.show', $category->id ?? '#') }}">
                                <i class="bi bi-{{ $category->icon ?? 'heart' }} me-2"></i>
                                {{ $category->name ?? 'Catégorie' }}
                                @if($category->is_popular ?? false)
                                    <span class="badge bg-success ms-auto">Populaire</span>
                                @endif
                            </a>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
