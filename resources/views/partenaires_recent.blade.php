<div class="row mt-4">
    <div class="col-12 mb-3">
        <h2 class="section-title">
            <i class="bi bi-stars me-2"></i> {{ __('messages.derniers_partenaires') }}
        </h2>
    </div>

    @forelse($recentProviders as $provider)
        <div class="col-md-3 mb-4">
            <div class="service-card h-100 text-center">
                <a href="{{ route('providers.show', $provider->id) }}" class="text-decoration-none text-dark">
                    @if($provider->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($provider->image->path))
                        <img
                            src="{{ Storage::url($provider->image->path) }}"
                            alt="{{ $provider->getFullName() }}"
                            class="img-fluid rounded-circle mb-2"
                            style="width: 80px; height: 80px; object-fit: cover; margin: 0 auto; display: block;"
                            onerror="this.src='{{ asset('images/placeholder.jpg') }}';">
                    @else
                        <div class="bg-light rounded-circle mb-2 mx-auto" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-person-circle" style="font-size: 2rem; color: #ccc;"></i>
                        </div>
                    @endif

                    <!-- Nom du prestataire -->
                    <h5 class="mb-1">{{ $provider->getFullName() }}</h5>

                    <!-- Catégories de services -->
                    @if($provider->serviceCategories->isNotEmpty())
                        <div class="mb-2">
                            @foreach($provider->serviceCategories->take(2) as $category)
                                <span class="badge bg-service mb-1">
                                    <i class="bi bi-{{ $category->icon ?? 'tag' }} me-1"></i>
                                    {{ Str::limit($category->name, 15) }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <!-- Adresse -->
                    <p class="text-muted small mb-0">
                        <i class="bi bi-geo-alt me-1"></i>
                        {{ Str::limit($provider->address ?? __('Adresse non spécifiée'), 20) }}
                    </p>
                </a>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info text-center py-3">
                <i class="bi bi-info-circle me-2"></i>
                {{ __('Aucun partenaire récent pour le moment.') }}
            </div>
        </div>
    @endforelse
</div>
