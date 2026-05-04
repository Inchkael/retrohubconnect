@extends('layouts.layout')

@section('title', 'Modifier le slider')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Modifier le slider: {{ $slider->title }}</h2>
            <a href="{{ route('admin.sliders.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour à la liste
            </a>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <form action="{{ route('admin.sliders.update', $slider) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="position" value="{{ $slider->position + 1 }}">

                    <div class="mb-3">
                        <label for="title" class="form-label">Titre du slider</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title', $slider->title) }}" required>
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Section Image avec gestion avancée -->
                    <div class="mb-3">
                        <label class="form-label">Image principale</label>

                        <!-- Zone de prévisualisation -->
                        <div class="image-container mb-3 text-center">
                            @if($slider->mainImage())
                                <img id="imagePreview" src="{{ asset('storage/' . $slider->mainImage()->path) }}"
                                     alt="Image actuelle"
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
                             id="dropArea">
                            <input type="file" class="d-none" id="image" name="image" accept="image/*">
                            <div class="drop-area-content">
                                <i class="bi bi-cloud-arrow-up display-4 mb-2"></i>
                                <p class="mb-0">Glissez-déposez une image ici</p>
                                <p class="small text-muted">ou cliquez pour sélectionner (max 10Mo)</p>
                            </div>
                        </div>

                        @error('image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check form-switch">
                        <!-- Champ caché pour s'assurer que la valeur est toujours envoyée -->
                        <input type="hidden" name="is_active" value="0">
                        <!-- Checkbox visible -->
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                               {{ old('is_active', $slider->is_active) ? 'checked' : '' }} value="1">
                        <label class="form-check-label" for="is_active">Actif</label>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Mettre à jour
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

<!-- Scripts pour les messages de session et le drag-and-drop -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Désactive les notifications natives de Laravel
        const alertElements = document.querySelectorAll('.alert');
        alertElements.forEach(alert => {
            alert.style.display = 'none';
        });

        // Affiche les messages avec SweetAlert2
        @if(session('success'))
        Swal.fire({
            title: 'Succès !',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6',
        });
        @endif

        @if(session('error'))
        Swal.fire({
            title: 'Erreur !',
            text: "{{ session('error') }}",
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#d33',
        });
        @endif

        // Récupération des éléments
        const dropArea = document.getElementById('dropArea');
        const fileInput = document.getElementById('image');
        let previewContainer = document.getElementById('imagePreview');

        // Fonction pour mettre à jour la prévisualisation
        function updatePreview(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if (previewContainer.tagName === 'IMG') {
                    const parent = previewContainer.parentNode;
                    const newDiv = document.createElement('div');
                    newDiv.id = 'imagePreview';
                    parent.replaceChild(newDiv, previewContainer);
                    previewContainer = newDiv;
                } else {
                    previewContainer.innerHTML = '';
                }

                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-fluid rounded mb-2';
                img.style.maxHeight = '200px';
                img.style.maxWidth = '100%';
                img.style.objectFit = 'contain';
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        }

        // Gestion du clic sur la zone de drop
        if (dropArea) {
            dropArea.addEventListener('click', function() {
                fileInput.click();
            });
        }

        // Gestion du changement de fichier
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const file = this.files[0];

                    if (!file.type.match('image.*')) {
                        Swal.fire({
                            title: 'Erreur !',
                            text: 'Seuls les fichiers image sont acceptés.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                        });
                        this.value = '';
                        return;
                    }

                    if (file.size > 10 * 1024 * 1024) {
                        Swal.fire({
                            title: 'Erreur !',
                            text: "L'image ne doit pas dépasser 10Mo.",
                            icon: 'error',
                            confirmButtonText: 'OK',
                        });
                        this.value = '';
                        return;
                    }

                    updatePreview(file);
                }
            });
        }

        // Gestion du drag-and-drop
        if (dropArea) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, unhighlight, false);
            });

            function highlight() {
                dropArea.classList.add('drag-over');
            }

            function unhighlight() {
                dropArea.classList.remove('drag-over');
            }

            dropArea.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;

                if (files && files.length > 0) {
                    const file = files[0];

                    if (!file.type.match('image.*')) {
                        Swal.fire({
                            title: 'Erreur !',
                            text: 'Seuls les fichiers image sont acceptés.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                        });
                        return;
                    }

                    if (file.size > 10 * 1024 * 1024) {
                        Swal.fire({
                            title: 'Erreur !',
                            text: "L'image ne doit pas dépasser 10Mo.",
                            icon: 'error',
                            confirmButtonText: 'OK',
                        });
                        return;
                    }

                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInput.files = dataTransfer.files;
                    updatePreview(file);
                }
            }
        }
    });
</script>
