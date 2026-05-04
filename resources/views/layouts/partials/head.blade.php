<meta name="csrf-token" content="{{ csrf_token() }}">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Plateforme Bien-Être : découvrez nos services de massage, yoga, méditation et coaching pour votre équilibre et santé.">
<meta name="keywords" content="bien-être, massage, yoga, méditation, coaching, santé">
<meta name="author" content="Espace de Mickael Collings">

<!-- Favicon -->
<link rel="icon" href="/images/favicon.avif" type="image/x-icon">

<!-- CSS -->
@stack('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Polyfill pour la reconnaissance vocale -->
<script src="https://cdn.jsdelivr.net/npm/@voxeo/speech-recognition-polyfill@0.2.0/dist/bundle.min.js"></script>

<title>@yield('title', 'RetroHubConnect | Accueil')</title>

@include('layouts.partials.styles')
