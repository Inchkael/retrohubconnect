<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Service de géocodage pour convertir des adresses en coordonnées géographiques
 * Utilise l'API Nominatim d'OpenStreetMap avec mise en cache des résultats
 * pour optimiser les performances et respecter les limites de requêtes
 */

class GeocodingService
{
    protected $client;
    protected $userAgent;
    protected $cacheTTL = 86400; // 24 heures

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 10.0,
            'headers' => [
                'User-Agent' => $this->getUserAgent()
            ]
        ]);
    }

    /**
     * Génère un User-Agent conforme aux règles de Nominatim
     */
    protected function getUserAgent(): string
    {
        if (isset($this->userAgent)) {
            return $this->userAgent;
        }

        return config('app.name', 'BienEtreApp') . '/1.0 (mickael.collings@hotmail.com)';
    }

    /**
     * Nettoie et formate une adresse pour le géocodage
     */
    public function cleanAddress(string $address): string
    {
        // Supprimer les sauts de ligne et espaces multiples
        $address = preg_replace('/\s+/', ' ', trim($address));

        // Remplacer les abréviations courantes
        $replacements = [
            '/\b(r|rue|rv)\b/i' => 'rue',
            '/\b(av|avenue)\b/i' => 'avenue',
            '/\b(bd|boulevard|bld)\b/i' => 'boulevard',
            '/\b(pl|place)\b/i' => 'place',
            '/\b(ch|chemin)\b/i' => 'chemin',
            '/\b(n°|no|#)\b/i' => '',
            '/\b(saint|st)\b/i' => 'saint',
            '/\b(ste)\b/i' => 'sainte',
        ];

        foreach ($replacements as $pattern => $replacement) {
            $address = preg_replace($pattern, $replacement, $address);
        }

        // Extraire et reformater le code postal et la localité
        if (preg_match('/(\d{4})\s+([^,]+)(?:,|$)/i', $address, $matches)) {
            $postalCode = $matches[1];
            $locality = trim($matches[2]);
            $streetPart = trim(preg_replace('/' . preg_quote($postalCode . ' ' . $locality) . '$/i', '', $address));

            return "$streetPart, $postalCode $locality, Belgique";
        }

        // Si pas de code postal trouvé, ajouter "Belgique" pour aider Nominatim
        if (strpos(strtolower($address), 'belgique') === false) {
            $address .= ', Belgique';
        }

        return $address;
    }

    /**
     * Géocode une adresse belge avec Nominatim
     */
    public function geocodeAddress(string $address): ?array
    {
        $cleanAddress = $this->cleanAddress($address);
        $cacheKey = 'geocode:' . md5(strtolower($cleanAddress));

        // Vérifier le cache
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            Log::debug("Tentative de géocodage pour: {$cleanAddress}");

            $response = $this->client->get('https://nominatim.openstreetmap.org/search', [
                'query' => [
                    'format' => 'json',
                    'q' => $cleanAddress,
                    'countrycodes' => 'be',
                    'limit' => 1,
                    'addressdetails' => 1
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            Log::debug("Réponse Nominatim: ", $data);

            if (!empty($data) && isset($data[0]['lat'], $data[0]['lon'])) {
                $result = [
                    'latitude' => (float)$data[0]['lat'],
                    'longitude' => (float)$data[0]['lon'],
                    'display_name' => $data[0]['display_name'] ?? $cleanAddress,
                    'raw_response' => $data[0]
                ];

                Cache::put($cacheKey, $result, $this->cacheTTL);
                return $result;
            }

            Log::warning("Aucun résultat trouvé pour: {$cleanAddress}");
            return null;

        } catch (RequestException $e) {
            Log::error("Erreur de requête Nominatim pour '{$cleanAddress}': " . $e->getMessage());
            if ($e->hasResponse()) {
                Log::error("Réponse d'erreur: " . $e->getResponse()->getBody());
            }
            return null;
        } catch (\Exception $e) {
            Log::error("Erreur de géocodage pour '{$cleanAddress}': " . $e->getMessage());
            return null;
        }
    }

    /**
     * Géocode l'adresse d'un utilisateur et met à jour ses coordonnées
     */
    public function geocodeUserAddress(User $user): bool
    {
        if (!$user->address) {
            Log::debug("Pas d'adresse à géocoder pour l'utilisateur {$user->id}");
            return false;
        }

        $result = $this->geocodeAddress($user->address);

        if ($result) {
            $user->update([
                'latitude' => $result['latitude'],
                'longitude' => $result['longitude']
            ]);

            Log::info("Géocodage réussi pour l'utilisateur {$user->id}: lat={$result['latitude']}, lon={$result['longitude']}");
            return true;
        }

        Log::warning("Échec du géocodage pour l'utilisateur {$user->id} (adresse: {$user->address})");
        return false;
    }
}
