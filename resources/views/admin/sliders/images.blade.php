<!-- resources/views/admin/sliders/images.blade.php -->
@extends('layouts.layout') <!-- Utilise ton layout existant -->

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestion des images du slider: {{ $slider->title }}</h2>
            <a href="{{ route('admin.sliders.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour à la liste
            </a>
        </div>

        <!-- Messages d'erreur/succès -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Section d'ajout d'image -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Ajouter une nouvelle image
            </div>
            <div class="card-body">
                <form action="{{ route('admin.sliders.images.store', $slider) }}" method="POST" enctype="multipart/form-data" class="dropzone" id="imageDropzone">
                    @csrf
                    <div class="dz-message">
                        <i class="bi bi-cloud-arrow-up display-4 text-muted"></i>
                        <p>Glissez-déposez une image ici ou cliquez pour sélectionner</p>
                        <p class="text-muted small">Formats supportés: JPG, PNG, GIF. Taille max: 2Mo</p>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des images existantes -->
        <div class="card">
            <div class="card-header bg-light">
                Images existantes ({{ $slider->images->count() }})
            </div>
            <div class="card-body">
                @if($slider->images->isEmpty())
                    <div class="alert alert-info">
                        Aucune image n'est associée à ce slider. Ajoutez-en une pour commencer.
                    </div>
                @else
                    <div class="row" id="images-container">
                        @foreach($slider->images->sortBy('position') as $image)
                            <div class="col-md-4 col-lg-3 mb-4" data-image-id="{{ $image->id }}">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-img-top overflow-hidden" style="height: 180px;">
                                        <img src="{{ $image->url }}"
                                             alt="Image du slider"
                                             class="img-fluid h-100 w-100"
                                             style="object-fit: cover;">
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">Position: {{ $image->position + 1 }}</small>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent p-2">
                                        <div class="d-flex justify-content-end gap-1">
                                            <!-- Bouton Supprimer -->
                                            <form action="{{ route('admin.sliders.images.destroy', [$slider, $image]) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Supprimer cette image"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette image?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/min/dropzone.min.css" rel="stylesheet">
    <style>
        .dropzone {
            min-height: 150px;
            border: 2px dashed #ccc;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .dropzone:hover {
            border-color: #6c757d;
            background-color: rgba(0, 0, 0, 0.02);
        }

        .dz-message {
            padding: 20px;
        }

        .dz-preview .dz-image {
            width: 120px;
            height: 120px;
        }

        .card-img-top {
            transition: transform 0.3s ease;
        }

        .card-img-top:hover {
            transform: scale(1.02);
        }

        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.15) !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/min/dropzone.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialisation de Dropzone
            Dropzone.autoDiscover = false;
            const dropzone = new Dropzone("#imageDropzone", {
                paramName: "image",
                maxFilesize: 2, // MB
                acceptedFiles: "image/jpeg,image/png,image/gif",
                addRemoveLinks: true,
                dictDefaultMessage: "Glissez-déposez une image ici ou cliquez pour sélectionner",
                dictRemoveFile: "Supprimer",
                dictMaxFilesExceeded: "Vous ne pouvez pas télécharger plus de fichiers",
                dictFileTooBig: "Le fichier est trop volumineux (max 2Mo).",

                init: function() {
                    this.on("success", function(file, response) {
                        if (response.success) {
                            // Rafraîchir la page pour voir la nouvelle image
                            window.location.reload();
                        } else {
                            this.removeFile(file);
                            alert(response.message || 'Une erreur est survenue');
                        }
                    });

                    this.on("error", function(file, message) {
                        this.removeFile(file);
                        alert(message);
                    });
                }
            });

            // Initialisation du glisser-déposer pour réorganiser les images
            const imagesContainer = document.getElementById('images-container');
            if (imagesContainer) {
                new Sortable(imagesContainer, {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: function(e) {
                        const imageOrders = {};
                        document.querySelectorAll('[data-image-id]').forEach((item, index) => {
                            const imageId = item.dataset.imageId;
                            imageOrders[imageId] = index;
                        });

                        // Envoyer la nouvelle ordre au serveur
                        fetch("{{ route('admin.sliders.images.updateOrder', $slider) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                imageOrders: imageOrders
                            })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (!data.success) {
                                    alert(data.message || 'Une erreur est survenue');
                                    // Recharger la page pour rétablir l'ordre
                                    location.reload();
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Une erreur est survenue lors de la mise à jour de l\'ordre des images.');
                                location.reload();
                            });
                    }
                });
            }
        });
    </script>
@endpush
