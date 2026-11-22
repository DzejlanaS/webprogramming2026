<?php
require_once __DIR__ . '/ExternalApiException.php';
require_once __DIR__ . '/../dao/ProductDao.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../utils/Logger.php';

class BreedFactService
{
    private const UNAVAILABLE = 'Breed facts are not available at the moment. Please try again later.';

    public function __construct(private ProductDao $productDao = new ProductDao()) {}

    public function getForProduct(int $productId): ?array
    {
        $product = $productId > 0 ? $this->productDao->getById($productId) : null;
        if ($product === null) {
            return null;
        }

        $animals = $product['pet_type'] === 'both' ? ['dog', 'cat'] : [$product['pet_type']];
        $facts = [];
        foreach ($animals as $animal) {
            try {
                $facts[] = $this->fetchRandomBreed($animal);
            } catch (ExternalApiException) {
                continue;
            }
        }

        if ($facts === []) {
            throw new ExternalApiException(self::UNAVAILABLE);
        }

        return [
            'product_id' => $product['id'],
            'pet_type'   => $product['pet_type'],
            'facts'      => $facts,
        ];
    }

    private function fetchRandomBreed(string $animal): array
    {
        $baseUrl = $animal === 'dog' ? Config::DOG_API_URL() : Config::CAT_API_URL();
        $apiKey = $animal === 'dog' ? Config::DOG_API_KEY() : Config::CAT_API_KEY();
        $source = parse_url($baseUrl, PHP_URL_HOST);
        $url = $baseUrl . '/images/search?has_breeds=1&limit=1';

        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => Config::EXTERNAL_API_TIMEOUT(),
            CURLOPT_TIMEOUT        => Config::EXTERNAL_API_TIMEOUT(),
            CURLOPT_HTTPHEADER     => ['Accept: application/json', 'x-api-key: ' . $apiKey],
        ]);

        $start = microtime(true);
        $body = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);
        $duration = (int) round((microtime(true) - $start) * 1000);

        if ($body === false) {
            $this->fail("EXTERNAL GET $source/images/search failed after {$duration}ms: $curlError");
        }
        if ($status !== 200) {
            $this->fail("EXTERNAL GET $source/images/search answered $status after {$duration}ms");
        }

        $images = json_decode($body, true);
        $image = is_array($images) ? ($images[0] ?? null) : null;
        $breed = is_array($image) && isset($image['breeds'][0]) && is_array($image['breeds'][0]) ? $image['breeds'][0] : null;
        if ($breed === null || !isset($breed['name'])) {
            $this->fail("EXTERNAL GET $source/images/search returned an unexpected response after {$duration}ms");
        }

        Logger::info("EXTERNAL GET $source/images/search $status {$duration}ms");

        return [
            'animal'      => $animal,
            'breed'       => $breed['name'],
            'temperament' => $breed['temperament'] ?? null,
            'life_span'   => isset($breed['life_span']) ? $this->withYears($breed['life_span']) : null,
            'breed_group' => $breed['breed_group'] ?? null,
            'origin'      => $breed['origin'] ?? null,
            'description' => $breed['description'] ?? null,
            'image_url'   => $image['url'] ?? null,
        ];
    }

    private function withYears(string $lifeSpan): string
    {
        $lifeSpan = trim($lifeSpan);
        return str_ends_with(strtolower($lifeSpan), 'years') ? $lifeSpan : $lifeSpan . ' years';
    }

    private function fail(string $logMessage): never
    {
        Logger::error($logMessage);
        throw new ExternalApiException(self::UNAVAILABLE);
    }
}
