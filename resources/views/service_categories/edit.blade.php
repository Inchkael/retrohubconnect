@extends('layouts.layout')

@section('title', 'Éditer la catégorie : ' . $category->name)

@section('content')
    <div class="container">
        <div class="service-card mb-4">
            <h1 class="text-center mb-4">
                <i class="bi bi-pencil-square me-2"></i>
                Éditer la catégorie {{ $category->name }}
            </h1>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.service_categories.update', $category->id) }}"
                  enctype="multipart/form-data" class="glassmorph-form p-4 rounded-4">
                @csrf
                @method('PUT')

                <!-- Champ pour le nom -->
                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">Nom de la catégorie</label>
                    <input type="text" class="form-control form-control-lg" id="name" name="name"
                           value="{{ old('name', $category->name) }}" required>
                </div>

                <!-- Champ pour la description -->
                <div class="mb-4">
                    <label for="description" class="form-label fw-bold">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $category->description) }}</textarea>
                </div>

                <!-- Section pour les images -->
                <div class="row g-4">
                    @for($i = 0; $i < 3; $i++)
                        @php
                            $image = $category->originalImages()->where('position', $i)->first();
                            $imageNumber = $i + 1;
                        @endphp
                        <div class="col-md-4">
                            <div class="card h-100 glassmorph-card">
                                <div class="card-body text-center">
                                    <h5 class="card-title mb-3">
                                        <i class="bi bi-image me-2"></i>
                                        Image {{ $imageNumber }}
                                    </h5>

                                    @if($image)
                                        <div class="mb-3 image-container">
                                            @php
                                                $baseName = pathinfo($image->path, PATHINFO_FILENAME);
                                                $baseName = preg_replace('/-original$/', '', $baseName);
                                            @endphp
                                            <picture>
                                                <source type="image/avif" srcset="
                                                {{ asset("storage/service_category_images/{$baseName}-380w.avif") }} 380w,
                                                {{ asset("storage/service_category_images/{$baseName}-540w.avif") }} 540w,
                                                {{ asset("storage/service_category_images/{$baseName}-700w.avif") }} 700w"
                                                        sizes="(max-width: 576px) 380px, (max-width: 768px) 540px, 700px">

                                                <source type="image/webp" srcset="
                                                {{ asset("storage/service_category_images/{$baseName}-380w.webp") }} 380w,
                                                {{ asset("storage/service_category_images/{$baseName}-540w.webp") }} 540w,
                                                {{ asset("storage/service_category_images/{$baseName}-700w.webp") }} 700w"
                                                        sizes="(max-width: 576px) 380px, (max-width: 768px) 540px, 700px">

                                                <source type="image/png" srcset="
                                                {{ asset("storage/service_category_images/{$baseName}-380w.png") }} 380w,
                                                {{ asset("storage/service_category_images/{$baseName}-540w.png") }} 540w,
                                                {{ asset("storage/service_category_images/{$baseName}-700w.png") }} 700w"
                                                        sizes="(max-width: 576px) 380px, (max-width: 768px) 540px, 700px">

                                                <img src="{{ $image->url }}"
                                                     srcset="
                                                     {{ asset("storage/service_category_images/{$baseName}-380w.jpg") }} 380w,
                                                     {{ asset("storage/service_category_images/{$baseName}-540w.jpg") }} 540w,
                                                     {{ asset("storage/service_category_images/{$baseName}-700w.jpg") }} 700w"
                                                     sizes="(max-width: 576px) 380px, (max-width: 768px) 540px, 700px"
                                                     alt="{{ $category->name }}"
                                                     class="img-fluid rounded mb-2"
                                                     loading="lazy"
                                                     decoding="async">
                                            </picture>

                                            <button type="button" class="btn btn-outline-danger btn-sm mt-2"
                                                    onclick="deleteImage('{{ $imageNumber }}', this)">
                                                <i class="bi bi-trash me-1"></i> Supprimer
                                            </button>
                                        </div>
                                    @else
                                        <div class="mb-3 image-container">
                                            <div class="p-4 bg-light rounded">
                                                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                                <p class="text-muted mt-2">Aucune image</p>
                                            </div>
                                            <button type="button" class="btn btn-outline-secondary btn-sm mt-2" disabled>
                                                <i class="bi bi-trash me-1"></i> Supprimer
                                            </button>
                                        </div>
                                    @endif

                                    <!-- Zone de drag-and-drop pour le téléchargement -->
                                    <div class="drop-area mb-3"
                                         ondragover="handleDragOver(event)"
                                         ondrop="handleDrop(event, '{{ $imageNumber }}')"
                                         ondragleave="handleDragLeave(event)"
                                         ondragenter="handleDragEnter(event)">
                                        <input type="file" class="d-none" id="image{{ $imageNumber }}-file"
                                               name="image{{ $imageNumber }}" accept="image/*">
                                        <div class="drop-area-content">
                                            <i class="bi bi-cloud-arrow-up display-4 mb-2"></i>
                                            <p class="mb-0">Glissez-déposez une image ici</p>
                                            <p class="small text-muted">ou cliquez pour sélectionner</p>
                                        </div>
                                        <div id="image{{ $imageNumber }}-preview" class="preview-container"></div>
                                    </div>

                                    <input type="hidden" name="delete_image{{ $imageNumber }}" id="delete_image{{ $imageNumber }}" value="0">
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('service_categories.show', $category->id) }}" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-arrow-left me-2"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-save me-2"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .glassmorph-form {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .glassmorph-card {
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .glassmorph-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            background: rgba(255, 255, 255, 0.6);
        }

        /* Styles pour la zone de drag-and-drop */
        .drop-area {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: rgba(255, 255, 255, 0.3);
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .drop-area:hover, .drop-area.drag-over {
            border-color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.1);
        }

        .drop-area-content {
            color: #6c757d;
        }

        .preview-container {
            margin-top: 10px;
            max-height: 100px;
            overflow: hidden;
        }

        .preview-container img {
            max-height: 100px;
            max-width: 100%;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .image-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        @media (max-width: 767.98px) {
            .glassmorph-form {
                padding: 1.5rem;
            }
            .card-body {
                padding: 1rem;
            }
            .drop-area {
                min-height: 100px;
                padding: 15px;
            }
        }
    </style>

    <script>
        // Fonction pour la suppression d'image
        function deleteImage(imageNumber, button) {
            Swal.fire({
                title: 'Supprimer cette image?',
                text: "Cette action ne peut pas être annulée!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer!',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete_image' + imageNumber).value = '1';

                    const container = button.closest('.image-container');
                    const img = container.querySelector('img, picture');
                    if (img) img.style.display = 'none';

                    button.innerHTML = '<i class="bi bi-check-circle me-1"></i> Supprimée';
                    button.classList.remove('btn-outline-danger');
                    button.classList.add('btn-outline-success');
                    button.disabled = true;
                    button.onclick = null;

                    Swal.fire('Supprimé!', 'L\'image sera supprimée lors de la sauvegarde.', 'success');
                }
            });
        }

        // Fonctions pour le drag-and-drop
        function handleDragOver(event) {
            event.preventDefault();
            event.stopPropagation();
            event.currentTarget.classList.add('drag-over');
        }

        function handleDragLeave(event) {
            event.preventDefault();
            event.stopPropagation();
            event.currentTarget.classList.remove('drag-over');
        }

        function handleDragEnter(event) {
            event.preventDefault();
            event.stopPropagation();
            event.currentTarget.classList.add('drag-over');
        }

        function handleDrop(event, imageNumber) {
            event.preventDefault();
            event.stopPropagation();
            event.currentTarget.classList.remove('drag-over');

            const fileInput = document.getElementById('image' + imageNumber + '-file');
            const previewContainer = document.getElementById('image' + imageNumber + '-preview');

            if (event.dataTransfer.files && event.dataTransfer.files.length > 0) {
                const file = event.dataTransfer.files[0];

                // Vérification du type de fichier
                if (!file.type.match('image.*')) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Seuls les fichiers image sont acceptés (JPEG, PNG, GIF, WebP).'
                    });
                    return;
                }

                // Vérification de la taille (2Mo max)
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'L\'image ne doit pas dépasser 2Mo.'
                    });
                    return;
                }

                // Afficher la prévisualisation
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.innerHTML = '';
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    previewContainer.appendChild(img);

                    // Mettre à jour le champ de fichier
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInput.files = dataTransfer.files;
                };
                reader.readAsDataURL(file);
            }
        }

        // Ajouter un clic sur la zone de drop pour ouvrir le sélecteur de fichiers
        document.querySelectorAll('.drop-area').forEach(dropArea => {
            dropArea.addEventListener('click', function() {
                const imageNumber = this.getAttribute('ondrop').match(/'([^']+)'/)[1];
                document.getElementById('image' + imageNumber + '-file').click();
            });
        });

        // Gérer la prévisualisation quand un fichier est sélectionné via le bouton
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    const imageNumber = this.name.replace('image', '');
                    const previewContainer = document.getElementById('image' + imageNumber + '-preview');

                    // Vérification du type de fichier
                    if (!this.files[0].type.match('image.*')) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Seuls les fichiers image sont acceptés.'
                        });
                        this.value = '';
                        return;
                    }

                    // Vérification de la taille
                    if (this.files[0].size > 2 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'L\'image ne doit pas dépasser 2Mo.'
                        });
                        this.value = '';
                        return;
                    }

                    reader.onload = function(e) {
                        previewContainer.innerHTML = '';
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        previewContainer.appendChild(img);
                    };

                    reader.readAsDataURL(this.files[0]);
                }
            });
        });

        // Validation du formulaire
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!document.getElementById('name').value.trim()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Le nom de la catégorie est obligatoire!'
                });
                document.getElementById('name').focus();
            }
        });
    </script>
@endsection
