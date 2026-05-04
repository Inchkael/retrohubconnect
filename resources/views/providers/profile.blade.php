@extends('layouts.layout')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h2>Mon Profil {{ Auth::user()->isProvider() ? 'Prestataire' : 'Utilisateur' }}</h2>
                    </div>
                    <div class="card-body">
                        <form id="userProfileForm" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">Prénom</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="{{ Auth::user()->first_name }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Nom</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="{{ Auth::user()->last_name }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ Auth::user()->email }}" readonly>
                                <small class="text-muted">L'email ne peut pas être modifié car il sert d'identifiant unique.</small>
                            </div>

                            <!-- Section Avatar/Logo avec gestion avancée -->
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="card h-100 glassmorph-card">
                                        <div class="card-body text-center">
                                            <h5 class="card-title mb-3">
                                                <i class="bi bi-image me-2"></i>
                                                {{ Auth::user()->isProvider() ? 'Logo' : 'Avatar' }}
                                            </h5>

                                            @php
                                                $imageField = Auth::user()->isProvider() ? 'logo' : 'avatar';
                                                $imagePath = Auth::user()->$imageField;
                                                $storagePath = Auth::user()->isProvider() ? 'logos' : 'avatars';
                                                $baseName = $imagePath ? pathinfo($imagePath, PATHINFO_FILENAME) : null;
                                            @endphp

                                            @if($imagePath)
                                                <div class="mb-3 image-container">
                                                    <picture>
                                                        @if($baseName)
                                                            <source type="image/avif" srcset="{{ asset("storage/{$storagePath}/{$baseName}-380w.avif") }} 380w,
                                                                                            {{ asset("storage/{$storagePath}/{$baseName}-540w.avif") }} 540w,
                                                                                            {{ asset("storage/{$storagePath}/{$baseName}-700w.avif") }} 700w"
                                                                    sizes="(max-width: 576px) 380px, (max-width: 768px) 540px, 700px">

                                                            <source type="image/webp" srcset="{{ asset("storage/{$storagePath}/{$baseName}-380w.webp") }} 380w,
                                                                                            {{ asset("storage/{$storagePath}/{$baseName}-540w.webp") }} 540w,
                                                                                            {{ asset("storage/{$storagePath}/{$baseName}-700w.webp") }} 700w"
                                                                    sizes="(max-width: 576px) 380px, (max-width: 768px) 540px, 700px">

                                                            <source type="image/png" srcset="{{ asset("storage/{$storagePath}/{$baseName}-380w.png") }} 380w,
                                                                                            {{ asset("storage/{$storagePath}/{$baseName}-540w.png") }} 540w,
                                                                                            {{ asset("storage/{$storagePath}/{$baseName}-700w.png") }} 700w"
                                                                    sizes="(max-width: 576px) 380px, (max-width: 768px) 540px, 700px">

                                                            <img src="{{ asset("storage/{$storagePath}/{$baseName}-original.jpg") }}"
                                                                 alt="{{ Auth::user()->isProvider() ? 'Logo' : 'Avatar' }}"
                                                                 class="img-fluid rounded mb-2"
                                                                 style="max-height: 150px; max-width: 150px; object-fit: contain;"
                                                                 onerror="this.onerror=null; this.src='/images/placeholder.jpg';">
                                                        @else
                                                            <img src="{{ asset("storage/{$storagePath}/{$imagePath}") }}"
                                                                 alt="{{ Auth::user()->isProvider() ? 'Logo' : 'Avatar' }}"
                                                                 class="img-fluid rounded mb-2"
                                                                 style="max-height: 150px; max-width: 150px; object-fit: contain;"
                                                                 onerror="this.onerror=null; this.src='/images/placeholder.jpg';">
                                                        @endif
                                                    </picture>
                                                    <button type="button" class="btn btn-outline-danger btn-sm mt-2" id="deleteImageBtn">
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
                                                 ondrop="handleDrop(event)"
                                                 ondragleave="handleDragLeave(event)"
                                                 ondragenter="handleDragEnter(event)">
                                                <input type="file" class="d-none" id="imageFile" name="avatar" accept="image/*">
                                                <div class="drop-area-content">
                                                    <i class="bi bi-cloud-arrow-up display-4 mb-2"></i>
                                                    <p class="mb-0">Glissez-déposez une image ici</p>
                                                    <p class="small text-muted">ou cliquez pour sélectionner</p>
                                                </div>
                                                <div id="imagePreview" class="preview-container"></div>
                                            </div>

                                            <input type="hidden" name="delete_image" id="deleteImage" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section spécifique aux prestataires -->
                            @if(Auth::user()->isProvider())
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4">{{ Auth::user()->description }}</textarea>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="address" class="form-label">Adresse</label>
                                        <input type="text" class="form-control" id="address" name="address" value="{{ Auth::user()->address }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="mobile_phone" class="form-label">Téléphone</label>
                                        <input type="text" class="form-control" id="mobile_phone" name="mobile_phone" value="{{ Auth::user()->mobile_phone }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="vat_number" class="form-label">Numéro de TVA</label>
                                        <input type="text" class="form-control" id="vat_number" name="vat_number" value="{{ Auth::user()->vat_number }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="website" class="form-label">Site Web</label>
                                        <input type="url" class="form-control" id="website" name="website" value="{{ Auth::user()->website }}">
                                    </div>
                                </div>
                            @endif

                            @if(Auth::user()->isProvider())
                                <!-- Section Services proposés -->
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="card-title mb-0">Services proposés</h5>
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#serviceCategoriesModal">
                                                    <i class="bi bi-pencil-square me-1"></i> Gérer
                                                </button>
                                            </div>

                                            @php
                                                $userCategories = Auth::user()->serviceCategories()->wherePivot('user_id', Auth::user()->id)->get();
                                            @endphp

                                            @if($userCategories->isNotEmpty())
                                                <div class="d-flex flex-wrap gap-2 mb-3">
                                                    @foreach($userCategories as $category)
                                                        <a href="{{ route('service_categories.show', $category->id) }}" class="text-decoration-none">
                                                            <span class="badge bg-service d-flex align-items-center" style="font-size: 0.9rem; padding: 0.5em 0.75em;">
                                                                <i class="bi bi-{{ $category->icon ?? 'tag' }} me-1"></i>
                                                                {{ $category->name }}
                                                                @if(!$category->is_validated)
                                                                    <small class="ms-1" data-bs-toggle="tooltip" title="En attente de validation">
                                                                        <i class="bi bi-clock-fill text-warning"></i>
                                                                    </small>
                                                                @endif
                                                            </span>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-muted mb-0">Vous ne proposez aucun service pour le moment.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Section des promotions -->
                            <div class="mb-4">
                                <h4>Promotions</h4>
                                @if($user->promotions && $user->promotions->isNotEmpty())
                                    <div class="mb-3">
                                        @foreach($user->promotions as $promotion)
                                            <div class="card mb-2">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ $promotion->Nom }}</h5>
                                                    <p class="card-text">{{ $promotion->Description }}</p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <small class="text-muted">Catégorie: {{ $promotion->categorieDeServices->name }}</small><br>
                                                            <small class="text-muted">Du {{ $promotion->Début->format('d/m/Y') }} au {{ $promotion->Fin->format('d/m/Y') }}</small>
                                                        </div>
                                                        <div>
                                                            @if($promotion->DocumentPdf)
                                                                <a href="{{ Storage::url($promotion->DocumentPdf) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                                    <i class="bi bi-file-earmark-pdf me-1"></i> Voir le PDF
                                                                </a>
                                                            @endif
                                                            <form action="{{ route('promotions.destroy', $promotion) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette promotion ?')">
                                                                    <i class="bi bi-trash me-1"></i> Supprimer
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Formulaire pour ajouter une nouvelle promotion -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Ajouter une promotion</h5>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST" action="{{ route('promotions.store') }}" enctype="multipart/form-data">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="Nom" class="form-label">Nom</label>
                                                <input type="text" class="form-control" id="Nom" name="Nom" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="Description" class="form-label">Description</label>
                                                <textarea class="form-control" id="Description" name="Description" rows="3" required></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="CategorieDeServicesID" class="form-label">Catégorie de service</label>
                                                <select class="form-select" id="CategorieDeServicesID" name="CategorieDeServicesID" required>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="Début" class="form-label">Date de début</label>
                                                    <input type="date" class="form-control" id="Début" name="Début" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="Fin" class="form-label">Date de fin</label>
                                                    <input type="date" class="form-control" id="Fin" name="Fin" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="AffichageDébut" class="form-label">Date d'affichage début</label>
                                                    <input type="date" class="form-control" id="AffichageDébut" name="AffichageDébut" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="AffichageFin" class="form-label">Date d'affichage fin</label>
                                                    <input type="date" class="form-control" id="AffichageFin" name="AffichageFin" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="pdf" class="form-label">Document PDF</label>
                                                <input type="file" class="form-control" id="pdf" name="pdf" accept=".pdf" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Ajouter la promotion</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-save me-1"></i> Enregistrer les modifications
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour gérer les catégories de services -->
    @if(Auth::user()->isProvider())
        <div class="modal fade" id="serviceCategoriesModal" tabindex="-1" aria-labelledby="serviceCategoriesModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="serviceCategoriesModalLabel">Gestion des services</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            Sélectionnez les catégories qui vous correspondent. Si vous ne trouvez pas votre catégorie, proposez-la ci-dessous.
                        </div>

                        <div class="mb-3">
                            <label for="new_category" class="form-label">Proposer une nouvelle catégorie</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="new_category" placeholder="Ex: Réparation de consoles rétro">
                                <button class="btn btn-outline-secondary" type="button" id="proposeNewCategory">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catégories disponibles</label>
                            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                <div class="form-check mb-2" id="categoriesContainer">
                                    <!-- Les catégories seront chargées dynamiquement ici -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn btn-primary" id="saveCategoriesBtn">
                            <i class="bi bi-save me-1"></i> Enregistrer les modifications
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Styles pour le drag-and-drop et les cartes -->
    <style>
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
    </style>

    <!-- Scripts pour le drag-and-drop et la gestion des images -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion du formulaire de profil
            const profileForm = document.getElementById('userProfileForm');
            if (profileForm) {
                profileForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const submitButton = this.querySelector('button[type="submit"]');
                    submitButton.disabled = true;
                    submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enregistrement...`;

                    try {
                        const formData = new FormData(this);
                        // Ne plus supprimer 'avatar' ni 'photos' pour permettre l'upload via le formulaire principal
                        // formData.delete('avatar');
                        // formData.delete('photos');

                        const response = await fetch("{{ route('user.profile.update') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: 'Succès',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            window.location.href = data.redirect;
                        } else {
                            await Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: data.message || 'Une erreur est survenue.',
                            });
                        }
                    } catch (error) {
                        console.error('Erreur:', error);
                        await Swal.fire({
                            icon: 'error',
                            title: 'Erreur technique',
                            text: 'Une erreur est survenue. Veuillez réessayer.',
                        });
                    } finally {
                        submitButton.disabled = false;
                        submitButton.innerHTML = `<i class="bi bi-save me-1"></i> Enregistrer les modifications`;
                    }
                });
            }

            // Gestion de la suppression de l'avatar/logo
            const deleteImageBtn = document.getElementById('deleteImageBtn');
            if (deleteImageBtn) {
                deleteImageBtn.addEventListener('click', function() {
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
                            document.getElementById('deleteImage').value = '1';
                            const container = this.closest('.image-container');
                            if (container) {
                                container.querySelector('img, picture').style.display = 'none';
                            }
                            this.innerHTML = '<i class="bi bi-check-circle me-1"></i> Supprimée';
                            this.classList.remove('btn-outline-danger');
                            this.classList.add('btn-outline-success');
                            this.disabled = true;
                            this.onclick = null;

                            Swal.fire('Supprimé!', 'L\'image sera supprimée lors de la sauvegarde.', 'success');
                        }
                    });
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

            function handleDrop(event) {
                event.preventDefault();
                event.stopPropagation();
                event.currentTarget.classList.remove('drag-over');

                const fileInput = document.getElementById('imageFile');
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
            const dropArea = document.querySelector('.drop-area');
            if (dropArea) {
                dropArea.addEventListener('click', function() {
                    document.getElementById('imageFile').click();
                });
            }

            // Gérer la prévisualisation quand un fichier est sélectionné via le bouton
            const imageFileInput = document.getElementById('imageFile');
            if (imageFileInput) {
                imageFileInput.addEventListener('change', function() {
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
            }

            // Gestion du téléchargement de l'avatar/logo
            const uploadAvatarBtn = document.getElementById('uploadAvatarBtn');
            if (uploadAvatarBtn) {
                uploadAvatarBtn.addEventListener('click', async function() {
                    const imageFileInput = document.getElementById('imageFile');
                    if (!imageFileInput.files.length) {
                        await Swal.fire({
                            icon: 'warning',
                            title: 'Attention',
                            text: 'Veuillez sélectionner un fichier.',
                        });
                        return;
                    }

                    const formData = new FormData();
                    formData.append('avatar', imageFileInput.files[0]);

                    const btn = this;
                    btn.disabled = true;
                    btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Téléchargement...`;

                    try {
                        const response = await fetch("{{ route('user.profile.upload_avatar') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: 'Succès',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            location.reload();
                        } else {
                            await Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: data.message || 'Une erreur est survenue.',
                            });
                        }
                    } catch (error) {
                        console.error('Erreur:', error);
                        await Swal.fire({
                            icon: 'error',
                            title: 'Erreur technique',
                            text: 'Une erreur est survenue. Veuillez réessayer.',
                        });
                    } finally {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-upload me-1"></i> Mettre à jour {{ Auth::user()->isProvider() ? "le logo" : "l\'avatar" }}';
                    }
                });
            }

            @if(Auth::user()->isProvider())
            // Gestion du téléchargement des photos (uniquement pour les prestataires)
            const uploadPhotosBtn = document.getElementById('uploadPhotosBtn');
            if (uploadPhotosBtn) {
                uploadPhotosBtn.addEventListener('click', async function() {
                    const photosInput = document.getElementById('photos');
                    if (!photosInput.files.length) {
                        await Swal.fire({
                            icon: 'warning',
                            title: 'Attention',
                            text: 'Veuillez sélectionner au moins une photo.',
                        });
                        return;
                    }

                    const formData = new FormData();
                    for (let i = 0; i < photosInput.files.length; i++) {
                        formData.append('photos[]', photosInput.files[i]);
                    }

                    const btn = this;
                    btn.disabled = true;
                    btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Téléchargement...`;

                    try {
                        const response = await fetch("{{ route('user.profile.upload_photos') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: 'Succès',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            location.reload();
                        } else {
                            await Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: data.message || 'Une erreur est survenue.',
                            });
                        }
                    } catch (error) {
                        console.error('Erreur:', error);
                        await Swal.fire({
                            icon: 'error',
                            title: 'Erreur technique',
                            text: 'Une erreur est survenue. Veuillez réessayer.',
                        });
                    } finally {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-plus-circle me-1"></i> Ajouter des photos';
                    }
                });
            }

            // Gestion de la suppression des photos (uniquement pour les prestataires)
            document.querySelectorAll('.delete-photo-btn').forEach(button => {
                button.addEventListener('click', async function() {
                    const photoIndex = this.getAttribute('data-photo-index');

                    const result = await Swal.fire({
                        title: 'Êtes-vous sûr?',
                        text: "Cette photo sera définitivement supprimée!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Oui, supprimer!',
                        cancelButtonText: 'Annuler'
                    });

                    if (result.isConfirmed) {
                        const btn = this;
                        btn.disabled = true;
                        btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;

                        try {
                            const response = await fetch(`{{ route('user.profile.delete_photo', ['photoIndex' => '__INDEX__']) }}`.replace('__INDEX__', photoIndex), {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                }
                            });

                            const data = await response.json();

                            if (data.success) {
                                await Swal.fire({
                                    icon: 'success',
                                    title: 'Succès',
                                    text: data.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                location.reload();
                            } else {
                                await Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: data.message || 'Une erreur est survenue.',
                                });
                            }
                        } catch (error) {
                            console.error('Erreur:', error);
                            await Swal.fire({
                                icon: 'error',
                                title: 'Erreur technique',
                                text: 'Une erreur est survenue. Veuillez réessayer.',
                            });
                        } finally {
                            btn.disabled = false;
                            btn.innerHTML = '<i class="bi bi-trash me-1"></i> Supprimer';
                        }
                    }
                });
            });
            @endif

            // Gestion de la copie dans le presse-papiers
            document.querySelectorAll('.copy-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const clipboardText = this.getAttribute('data-clipboard-text');
                    navigator.clipboard.writeText(clipboardText).then(() => {
                        const originalHtml = this.innerHTML;
                        this.innerHTML = '<i class="bi bi-check"></i> Copié!';
                        this.classList.replace('btn-outline-secondary', 'btn-outline-success');

                        setTimeout(() => {
                            this.innerHTML = originalHtml;
                            this.classList.replace('btn-outline-success', 'btn-outline-secondary');
                        }, 2000);
                    }).catch(err => {
                        console.error('Échec de la copie: ', err);
                    });
                });
            });

            // Gestion des catégories de services (uniquement pour les prestataires)
            @if(Auth::user()->isProvider())
            const modal = document.getElementById('serviceCategoriesModal');
            if (modal) {
                const modalInstance = new bootstrap.Modal(modal);
                let userCategories = [];

                // Charger les catégories au chargement du modal
                modal.addEventListener('shown.bs.modal', function() {
                    loadCategories();
                });

                async function loadCategories() {
                    try {
                        const response = await fetch("{{ route('user.profile.get_service_categories') }}", {
                            method: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            }
                        });

                        if (!response.ok) {
                            throw new Error(`Erreur HTTP! Statut: ${response.status}`);
                        }

                        const data = await response.json();
                        const container = document.getElementById('categoriesContainer');
                        if (!container) {
                            console.error("Le conteneur 'categoriesContainer' n'existe pas !");
                            return;
                        }

                        if (!data.all_categories) {
                            console.error("Aucune catégorie n'est présente dans les données reçues.");
                            container.innerHTML = '<div class="alert alert-warning py-2 mb-0">Aucune catégorie disponible.</div>';
                            return;
                        }

                        userCategories = data.user_categories ? data.user_categories.map(category => category.id) : [];

                        if (data.all_categories.length > 0) {
                            let html = '';
                            data.all_categories.forEach(category => {
                                const isChecked = userCategories.includes(category.id);
                                const disabled = !category.is_validated ? ' disabled' : '';
                                const textMuted = !category.is_validated ? ' text-muted' : '';
                                const validationInfo = !category.is_validated ?
                                    `<small class="text-warning ms-1" data-bs-toggle="tooltip" title="En attente de validation">
                                        <i class="bi bi-clock-fill"></i>
                                    </small>` : '';

                                html += `
                                    <div class="form-check${textMuted}">
                                        <input class="form-check-input" type="checkbox" name="selected_categories[]"
                                               value="${category.id}" id="category_${category.id}"
                                               ${isChecked ? 'checked' : ''}${disabled}>
                                        <label class="form-check-label" for="category_${category.id}">
                                            <i class="bi bi-${category.icon ?? 'tag'} me-1"></i>
                                            ${category.name}
                                            ${validationInfo}
                                        </label>
                                    </div>
                                `;
                            });
                            container.innerHTML = html;

                            // Initialiser les tooltips
                            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                            tooltipTriggerList.map(function (tooltipTriggerEl) {
                                return new bootstrap.Tooltip(tooltipTriggerEl);
                            });
                        } else {
                            container.innerHTML = '<div class="alert alert-warning py-2 mb-0">Aucune catégorie disponible.</div>';
                        }
                    } catch (error) {
                        console.error('Erreur:', error);
                        const container = document.getElementById('categoriesContainer');
                        if (container) {
                            container.innerHTML = '<div class="alert alert-danger py-2 mb-0">Erreur lors du chargement des catégories.</div>';
                        }
                    }
                }

                // Gestion de la proposition de nouvelle catégorie
                document.getElementById('proposeNewCategory').addEventListener('click', function() {
                    const newCategoryInput = document.getElementById('new_category');
                    const newCategory = newCategoryInput.value.trim();

                    if (newCategory) {
                        const container = document.getElementById('categoriesContainer');
                        const newCategoryId = `new_${Date.now()}`;
                        const newCategoryHtml = `
                            <div class="form-check" id="new_category_${newCategoryId}">
                                <input class="form-check-input" type="checkbox" name="selected_categories[]"
                                       value="${newCategoryId}" checked>
                                <label class="form-check-label text-muted">
                                    <i class="bi bi-tag me-1"></i>
                                    ${newCategory}
                                    <small class="text-warning ms-1" data-bs-toggle="tooltip" title="En attente de validation">
                                        <i class="bi bi-clock-fill"></i>
                                    </small>
                                </label>
                                <input type="hidden" name="new_category_${newCategoryId}" value="${newCategory}">
                                <button type="button" class="btn btn-sm btn-outline-danger float-end remove-new-category"
                                        data-category-id="${newCategoryId}">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        `;
                        container.insertAdjacentHTML('beforeend', newCategoryHtml);
                        newCategoryInput.value = '';

                        // Réinitialiser les tooltips
                        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        tooltipTriggerList.map(function (tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl);
                        });
                    }
                });

                // Gestion de la suppression des nouvelles catégories proposées (avant enregistrement)
                document.getElementById('categoriesContainer').addEventListener('click', function(e) {
                    if (e.target.closest('.remove-new-category')) {
                        const categoryId = e.target.closest('.remove-new-category').getAttribute('data-category-id');
                        document.getElementById(`new_category_${categoryId}`).remove();
                    }
                });

                // Gestion de l'enregistrement des catégories
                document.getElementById('saveCategoriesBtn').addEventListener('click', async function() {
                    const selectedCategories = [];
                    const newCategories = [];

                    document.querySelectorAll('input[name="selected_categories[]"]:checked:not([disabled])').forEach(el => {
                        if (!el.value.startsWith('new_')) {
                            selectedCategories.push(parseInt(el.value));
                        } else {
                            const categoryId = el.value;
                            const input = document.querySelector(`input[name="new_category_${categoryId}"]`);
                            if (input) {
                                newCategories.push(input.value);
                            }
                        }
                    });

                    const btn = this;
                    btn.disabled = true;
                    btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enregistrement...`;

                    try {
                        const response = await fetch("{{ route('user.profile.update_service_categories') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                selected_categories: selectedCategories,
                                new_categories: newCategories
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: 'Succès',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            modalInstance.hide();
                            location.reload();
                        } else {
                            await Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: data.message || 'Une erreur est survenue.',
                            });
                        }
                    } catch (error) {
                        console.error('Erreur:', error);
                        await Swal.fire({
                            icon: 'error',
                            title: 'Erreur technique',
                            text: 'Une erreur est survenue. Veuillez réessayer.',
                        });
                    } finally {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-save me-1"></i> Enregistrer les modifications';
                    }
                });
            }
            @endif
        });
    </script>
@endsection
