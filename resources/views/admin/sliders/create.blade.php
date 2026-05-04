@extends('layouts.layout')

@section('title', 'Créer un nouveau slider')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Créer un nouveau slider</h2>
            <a href="{{ route('admin.sliders.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour à la liste
            </a>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="card shadow">
            <div class="card-body">
                <form action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="title" class="form-label">Titre du slider</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Section Image avec gestion avancée -->
                    <div class="mb-3">
                        <label class="form-label">Image principale</label>

                        <!-- Zone de prévisualisation -->
                        <div class="image-container mb-3 text-center">
                            @if(old('image'))
                                <img id="imagePreview" src="{{ old('image') }}"
                                     alt="Prévisualisation"
                                     class="img-fluid rounded mb-2"
                                     style="max-height: 200px; max-width: 100%; object-fit: contain;">
                            @else
                                <div id="imagePreview" class="p-4 bg-light rounded mb-2">
                                    <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2">Aucune image sélectionnée</p>
                                </div>
                            @endif
                        </div>

                        <!-- Zone de drag-and-drop -->
                        <div class="drop-area mb-3"
                             ondragover="handleDragOver(event)"
                             ondrop="handleDrop(event)"
                             ondragleave="handleDragLeave(event)"
                             ondragenter="handleDragEnter(event)">
                            <input type="file" class="d-none" id="image" name="image" accept="image/*" required>
                            <div class="drop-area-content">
                                <i class="bi bi-cloud-arrow-up display-4 mb-2"></i>
                                <p class="mb-0">Glissez-déposez une image ici</p>
                                <p class="small text-muted">ou cliquez pour sélectionner (max 10Mo, sera compressé à < 2Mo)</p>
                            </div>
                        </div>

                        @error('image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                        <label class="form-check-label" for="is_active">Actif</label>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

<!-- Styles pour le drag-and-drop -->
<style>
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

    .image-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
</style>

<!-- Scripts pour le drag-and-drop et la prévisualisation -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion du drag-and-drop
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

        function handleDrop(event) {
            event.preventDefault();
            event.stopPropagation();
            event.currentTarget.classList.remove('drag-over');

            const fileInput = document.getElementById('image');
            const previewContainer = document.getElementById('imagePreview');

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

                // Afficher la prévisualisation
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.innerHTML = '';
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-fluid rounded mb-2';
                    img.style.maxHeight = '200px';
                    img.style.maxWidth = '100%';
                    img.style.objectFit = 'contain';
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
        const dropArea = document.querySelector('.drop-area');
        if (dropArea) {
            dropArea.addEventListener('click', function() {
                document.getElementById('image').click();
            });
        }

        // Gérer la prévisualisation quand un fichier est sélectionné via le bouton
        const imageInput = document.getElementById('image');
        if (imageInput) {
            imageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    const previewContainer = document.getElementById('imagePreview');

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

                    // Vérification de la taille (10Mo max, sera compressé côté serveur)
                    if (this.files[0].size > 10 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'L\'image ne doit pas dépasser 10Mo (elle sera compressée à moins de 2Mo).'
                        });
                        this.value = '';
                        return;
                    }

                    reader.onload = function(e) {
                        previewContainer.innerHTML = '';
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'img-fluid rounded mb-2';
                        img.style.maxHeight = '200px';
                        img.style.maxWidth = '100%';
                        img.style.objectFit = 'contain';
                        previewContainer.appendChild(img);
                    };

                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
    });
</script>
