<!--
====================================================================================================
FICHIER : resources/views/home/last_items.blade.php
----------------------------------------------------------------------------------------------------
Description : Section affichant les 3 derniers articles (items) ajoutés.
----------------------------------------------------------------------------------------------------
-->

@if($lastItems && $lastItems->count() > 0)
    <div class="container section-container mt-5">
        <div class="section-header d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title">
                <i class="fas fa-clock me-2"></i> {{ __('Derniers articles') }}
            </h2>
        </div>

        <div class="row">
            @foreach($lastItems as $item)
                <div class="col-md-4 mb-4">
                    <div class="card item-card h-100">
                        <img src="{{ $item->image_url ?? asset('images/default-item.png') }}"
                             alt="{{ $item->name }}"
                             class="card-img-top"
                             style="height: 180px; object-fit: cover;">
                        <div class="card-body">
                            <h6 class="card-title">{{ $item->name }}</h6>
                            <p class="card-text text-muted small">
                                {{ Str::limit($item->description, 80) }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-retro">
                                <i class="fas fa-tag me-1"></i> {{ $item->price }} €
                            </span>
                                <span class="badge bg-retro">
                                <i class="fas fa-clock me-1"></i> {{ $item->created_at->diffForHumans() }}
                            </span>
                            </div>
                            <a href="{{ route('items.show', $item) }}" class="btn btn-retro btn-sm mt-2 w-100">
                                <i class="fas fa-eye me-1"></i> {{ __('Détails') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
