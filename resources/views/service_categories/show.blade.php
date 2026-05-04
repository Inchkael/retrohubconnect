@extends('layouts.layout')

@section('title', 'Détails de la Catégorie : ' . $category->name)

@section('content')
    <!-- Messages d'erreur/succès -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="container">
        <!-- Section description -->
        <div class="service-card mb-4">
            <h1>Description du service {{ $category->name }}</h1>
            <p>{{ $category->description }}</p>
        </div>

        <!-- Galerie d'images -->
        <div class="row mb-4">
            @foreach($category->originalImages as $image)
                <div class="col-md-4 mb-4">
                    @php
                        $baseName = pathinfo($image->path, PATHINFO_FILENAME);
                        $baseName = preg_replace('/-original$/', '', $baseName);
                        $sizes = [
                            '380' => '(max-width: 576px) 380px',
                            '540' => '(max-width: 768px) 540px',
                            '700' => '700px'
                        ];
                    @endphp

                    <picture>
                        <!-- AVIF - Meilleure compression -->
                        <source type="image/avif" srcset="
                            {{ asset("storage/service_category_images/{$baseName}-380w.avif") }} 380w,
                            {{ asset("storage/service_category_images/{$baseName}-540w.avif") }} 540w,
                            {{ asset("storage/service_category_images/{$baseName}-700w.avif") }} 700w"
                                sizes="{{ implode(', ', $sizes) }}">

                        <!-- WebP - Bon compromis -->
                        <source type="image/webp" srcset="
                            {{ asset("storage/service_category_images/{$baseName}-380w.webp") }} 380w,
                            {{ asset("storage/service_category_images/{$baseName}-540w.webp") }} 540w,
                            {{ asset("storage/service_category_images/{$baseName}-700w.webp") }} 700w"
                                sizes="{{ implode(', ', $sizes) }}">

                        <!-- PNG - Transparence -->
                        <source type="image/png" srcset="
                            {{ asset("storage/service_category_images/{$baseName}-380w.png") }} 380w,
                            {{ asset("storage/service_category_images/{$baseName}-540w.png") }} 540w,
                            {{ asset("storage/service_category_images/{$baseName}-700w.png") }} 700w"
                                sizes="{{ implode(', ', $sizes) }}">

                        <!-- Fallback JPEG -->
                        <img src="{{ $image->url }}"
                             srcset="
                                 {{ asset("storage/service_category_images/{$baseName}-380w.jpg") }} 380w,
                                 {{ asset("storage/service_category_images/{$baseName}-540w.jpg") }} 540w,
                                 {{ asset("storage/service_category_images/{$baseName}-700w.jpg") }} 700w"
                             sizes="{{ implode(', ', $sizes) }}"
                             alt="{{ $category->name }}"
                             class="img-fluid rounded"
                             loading="lazy"
                             decoding="async">
                    </picture>
                </div>
            @endforeach

            @for($i = $category->originalImages()->count() + 1; $i <= 3; $i++)
                <div class="col-md-4 mb-4">
                    <img src="{{ asset('images/placeholder.jpg') }}"
                         alt="{{ $category->name }}"
                         class="img-fluid rounded"
                         loading="lazy">
                </div>
            @endfor
        </div>

        <!-- Formulaire de recherche -->
        <div class="service-card mb-4">
            <h3>Rechercher ce service dans une région</h3>
            <form method="GET" action="{{ route('service_categories.show', $category->id) }}" class="input-group">
                <input type="text" class="form-control" name="region" placeholder="Ex: Liège, Bruxelles, Namur..." value="{{ $region ?? '' }}">
                <button class="btn btn-primary" type="submit">Rechercher</button>
            </form>
        </div>

        <!-- Liste des prestataires -->
        @if(isset($providers) && $providers->isNotEmpty())
            <div class="service-card mb-4">
                <h3>Prestataires proposant ce service @if($region) dans la région de {{ $region }}@endif</h3>
                <div class="row">
                    @foreach($providers as $provider)
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('providers.show', $provider) }}" class="text-decoration-none">
                                <div class="card h-100 clickable-card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $provider->last_name }} {{ $provider->first_name }}</h5>
                                        <p class="card-text">
                                            @if($provider->address)
                                                <i class="bi bi-geo-alt"></i> {{ $provider->address }}<br>
                                            @endif
                                            @if($provider->mobile_phone)
                                                <i class="bi bi-telephone"></i> {{ $provider->mobile_phone }}<br>
                                            @endif
                                            @if($provider->website)
                                                <i class="bi bi-globe"></i> <span>{{ $provider->website }}</span><br>
                                            @endif
                                            <strong>Services:</strong>
                                            @foreach($provider->serviceCategories as $serviceCategory)
                                                <a href="{{ route('service_categories.show', $serviceCategory->id) }}" class="badge bg-secondary text-decoration-none">
                                                    {{ $serviceCategory->name }}
                                                </a>
                                            @endforeach
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $providers->appends(request()->query())->links() }}
                </div>
            </div>
        @else
            <div class="alert alert-info">
                Aucun prestataire trouvé@if($region) dans la région {{ $region }}@endif.
            </div>
        @endif
    </div>

    <style>
        .clickable-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(12px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .clickable-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            background: rgba(255, 255, 255, 0.4);
            cursor: pointer;
        }
        .clickable-card .card-body {
            color: #212529;
            padding: 1rem;
        }
        .clickable-card .card-title {
            color: #0d6efd;
            margin-bottom: 0.75rem;
        }
        @supports not (backdrop-filter: blur(10px)) {
            .clickable-card {
                background: rgba(255, 255, 255, 0.9) !important;
            }
        }
        img[loading="lazy"] {
            background-color: #f8f9fa;
        }
        picture {
            display: block;
            width: 100%;
        }
        picture img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }

        .bg-secondary {
            background-color: #48D1CC !important;
            color: white;
        }
    </style>
@endsection
