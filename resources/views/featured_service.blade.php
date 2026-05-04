@if($monthService)
    <div class="row">
        <div class="container mt-4">
            <div class="service-card featured-service text-center">
                <h2>{{ __('messages.service_du_mois') }}</h2>
                <h3>{{ $monthService->name }}</h3>
                <p>{{ $monthService->description }}</p>

                <!-- Galerie d'images optimisées (3 images max, centrées) -->
                <div class="row mb-4 justify-content-center">
                    @forelse($monthService->originalImages->take(3) as $image)
                        <div class="col-md-4 col-lg-3 mb-4"> <!-- 3 colonnes sur écrans larges, 4 sur moyens -->
                            @php
                                $baseName = pathinfo($image->path, PATHINFO_FILENAME);
                                $baseName = preg_replace('/-original$/', '', $baseName);
                                $sizes = [
                                    '380' => '(max-width: 576px) 380px',
                                    '540' => '(max-width: 992px) 540px',
                                    '700' => '700px'
                                ];
                            @endphp

                            <picture>
                                <!-- AVIF -->
                                <source type="image/avif" srcset="
                                    {{ asset("storage/service_category_images/{$baseName}-380w.avif") }} 380w,
                                    {{ asset("storage/service_category_images/{$baseName}-540w.avif") }} 540w,
                                    {{ asset("storage/service_category_images/{$baseName}-700w.avif") }} 700w"
                                        sizes="{{ implode(', ', $sizes) }}">

                                <!-- WebP -->
                                <source type="image/webp" srcset="
                                    {{ asset("storage/service_category_images/{$baseName}-380w.webp") }} 380w,
                                    {{ asset("storage/service_category_images/{$baseName}-540w.webp") }} 540w,
                                    {{ asset("storage/service_category_images/{$baseName}-700w.webp") }} 700w"
                                        sizes="{{ implode(', ', $sizes) }}">

                                <!-- PNG -->
                                <source type="image/png" srcset="
                                    {{ asset("storage/service_category_images/{$baseName}-380w.png") }} 380w,
                                    {{ asset("storage/service_category_images/{$baseName}-540w.png") }} 540w,
                                    {{ asset("storage/service_category_images/{$baseName}-700w.png") }} 700w"
                                        sizes="{{ implode(', ', $sizes) }}">

                                <!-- Fallback JPEG -->
                                <img
                                    src="{{ $image->url }}"
                                    srcset="
                                        {{ asset("storage/service_category_images/{$baseName}-380w.jpg") }} 380w,
                                        {{ asset("storage/service_category_images/{$baseName}-540w.jpg") }} 540w,
                                        {{ asset("storage/service_category_images/{$baseName}-700w.jpg") }} 700w"
                                    sizes="{{ implode(', ', $sizes) }}"
                                    alt="{{ $monthService->name }}"
                                    class="img-fluid rounded"
                                    style="max-height: 200px; object-fit: contain; width: 100%;"
                                    loading="lazy"
                                    decoding="async"
                                    onerror="this.src='/images/placeholder.jpg'; this.onerror=null;">
                            </picture>
                        </div>
                    @empty
                        <!-- Placeholders si aucune image -->
                        @for($i = 0; $i < 3; $i++)
                            <div class="col-md-4 col-lg-3 mb-4">
                                <img src="{{ asset('images/placeholder.jpg') }}"
                                     alt="{{ $monthService->name }}"
                                     class="img-fluid rounded"
                                     style="max-height: 200px; object-fit: contain; width: 100%;"
                                     loading="lazy">
                            </div>
                        @endfor
                    @endforelse
                </div>

                <a href="{{ route('service_categories.show', $monthService->id) }}" class="btn btn-primary mt-3">{{ __('messages.voir_les_prestataires') }}</a>
            </div>
        </div>
    </div>
@endif
