<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\GeocodingService;
use Illuminate\Support\Facades\Log;

/**
 * Commande Artisan GeocodeMissingAddresses
 *
 * Cette commande console permet de géocoder les adresses manquantes des utilisateurs.
 * Elle est particulièrement utile pour :
 * - Compléter les coordonnées géographiques des utilisateurs existants
 * - Mettre à jour les coordonnées après une migration ou une importation de données
 * - Corriger les adresses qui n'ont pas été géocodées automatiquement
 *
 * Fonctionnalités principales :
 * - Géocodage des adresses manquantes
 * - Mode simulation (dry-run) pour tester sans modifier la base de données
 * - Option pour forcer le géocodage même si des coordonnées existent déjà
 * - Mode debug pour analyser les échecs
 * - Affichage des détails des échecs
 * - Barre de progression pour le suivi de l'exécution
 * - Statistiques de succès/échecs
 *
 * Utilisation typique :
 * php artisan users:geocode-missing
 * php artisan users:geocode-missing --limit=10
 * php artisan users:geocode-missing --dry-run
 * php artisan users:geocode-missing --force
 * php artisan users:geocode-missing --debug
 */

class GeocodeMissingAddresses extends Command
{
    protected $signature = 'users:geocode-missing
                            {--limit=50 : Nombre maximum d\'utilisateurs à traiter}
                            {--dry-run : Mode simulation (sans sauvegarde)}
                            {--force : Forcer le géocodage même si des coordonnées existent}
                            {--debug : Activer le mode debug pour analyser les échecs}
                            {--show-failures : Afficher les détails des échecs}';

    protected $description = 'Géocode les adresses manquantes des utilisateurs';

    protected $geocodingService;

    public function __construct(GeocodingService $geocodingService)
    {
        parent::__construct();
        $this->geocodingService = $geocodingService;
    }

    public function handle()
    {
        $limit = (int)$this->option('limit');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        $debug = $this->option('debug');
        $showFailures = $this->option('show-failures');

        $query = User::whereNotNull('address');

        if (!$force) {
            $query->where(function($q) {
                $q->whereNull('latitude')
                    ->orWhereNull('longitude');
            });
        }

        $users = $query->when($limit, function($q, $limit) {
            return $q->limit($limit);
        })->get();

        $this->info("Trouvé " . $users->count() . " utilisateur(s) à traiter.");

        if ($users->isEmpty()) {
            $this->info("Aucun utilisateur à géocoder.");
            return 0;
        }

        $bar = $this->output->createProgressBar($users->count());
        $successCount = 0;
        $failCount = 0;
        $failures = [];

        foreach ($users as $user) {
            if ($debug) {
                $this->debugUser($user);
            }

            try {
                $result = $this->geocodingService->geocodeAddress($user->address);

                if ($result) {
                    if (!$dryRun) {
                        $user->update([
                            'latitude' => $result['latitude'],
                            'longitude' => $result['longitude']
                        ]);
                    }
                    $successCount++;
                    $this->line("\n<fg=green>✓ Succès pour {$user->getFullName()}: lat={$result['latitude']}, lon={$result['longitude']}</>");
                } else {
                    $failCount++;
                    $failures[] = [
                        'id' => $user->id,
                        'name' => $user->getFullName(),
                        'address' => $user->address,
                        'clean_address' => $this->geocodingService->cleanAddress($user->address)
                    ];
                    $this->line("\n<fg=yellow>⚠ Échec pour {$user->getFullName()}: adresse non trouvée</>");
                }
            } catch (\Exception $e) {
                $failCount++;
                $failures[] = [
                    'id' => $user->id,
                    'name' => $user->getFullName(),
                    'address' => $user->address,
                    'error' => $e->getMessage()
                ];
                $this->line("\n<fg=red>✗ Erreur pour {$user->getFullName()}: {$e->getMessage()}</>");
            }

            $bar->advance();
        }

        $bar->finish();

        $this->info("\n\nRésultat:");
        $this->line("<fg=green>✓ Réussis: {$successCount}</>");
        $this->line("<fg=red>✗ Échecs: {$failCount}</>");

        if ($dryRun) {
            $this->warn("\nMode simulation activé: aucune modification n'a été enregistrée.");
        }

        if ($showFailures && !empty($failures)) {
            $this->showFailures($failures);
        }

        return 0;
    }

    protected function debugUser(User $user)
    {
        $this->line("\n<fg=cyan>=== DEBUG UTILISATEUR ===</>");
        $this->line("ID: <fg=yellow>{$user->id}</>");
        $this->line("Nom: <fg=yellow>{$user->getFullName()}</>");
        $this->line("Adresse brute: <fg=yellow>{$user->address}</>");

        $cleanAddress = $this->geocodingService->cleanAddress($user->address);
        $this->line("Adresse nettoyée: <fg=yellow>{$cleanAddress}</>");

        try {
            $result = $this->geocodingService->geocodeAddress($user->address);
            if ($result) {
                $this->line("✓ Géocodage réussi: lat={$result['latitude']}, lon={$result['longitude']}");
                $this->line("  Adresse trouvée: {$result['display_name']}");
            } else {
                $this->line("✗ Échec du géocodage");
            }
        } catch (\Exception $e) {
            $this->line("✗ Erreur: {$e->getMessage()}");
        }
    }

    protected function showFailures(array $failures)
    {
        $this->newLine(2);
        $this->info("Détails des échecs de géocodage:");

        $tableRows = [];
        foreach ($failures as $failure) {
            $tableRows[] = [
                'ID' => $failure['id'],
                'Nom' => $failure['name'],
                'Adresse brute' => $failure['address'],
                'Adresse nettoyée' => $failure['clean_address'] ?? 'N/A',
                'Erreur' => $failure['error'] ?? 'Adresse non trouvée'
            ];
        }

        $this->table(['ID', 'Nom', 'Adresse brute', 'Adresse nettoyée', 'Erreur'], $tableRows);
    }
}
