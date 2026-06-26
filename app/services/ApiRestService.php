<?php
class ApiRestService {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function get(string $endpoint): ?array {
        // 1. Verificar caché local
        $cached = $this->getCache($endpoint);
        if ($cached !== null) {
            return $cached;
        }

        // 2. Obtener datos (Mock o Real)
        if (API_USE_MOCK) {
            $data = $this->getFromMock($endpoint);
        } else {
            $data = $this->getFromApi($endpoint);
        }

        if ($data === null) {
            // API falló: intentar caché expirado
            return $this->getCache($endpoint, true);
        }

        // 3. Guardar en caché
        $this->setCache($endpoint, $data);

        return $data;
    }

    private function getFromMock(string $endpoint): ?array {
        $filename = ltrim($endpoint, '/') . '.json';
        $path = __DIR__ . '/../../tests/mock_api/' . $filename;

        if (file_exists($path)) {
            $raw = file_get_contents($path);
            return json_decode($raw, true);
        }
        return null;
    }

    private function getFromApi(string $endpoint): ?array {
        $url = rtrim(API_BASE_URL, '/') . '/' . ltrim($endpoint, '/');
        $ctx = stream_context_create([
            'http' => [
                'timeout' => API_TIMEOUT,
                'header'  => "Accept: application/json\r\n" .
                             "Authorization: Bearer " . API_BEARER_TOKEN . "\r\n",
            ]
        ]);

        $raw = @file_get_contents($url, false, $ctx);
        if ($raw === false) return null;

        $data = json_decode($raw, true);
        return (isset($data['status']) && $data['status'] === 'success') ? $data : null;
    }

    private function getCache(string $endpoint, bool $ignoreExpiry = false): ?array {
        $cache = new ApiCache();
        return $cache->get($endpoint, $ignoreExpiry);
    }

    private function setCache(string $endpoint, array $data): void {
        $cache = new ApiCache();
        $cache->set($endpoint, $data, API_CACHE_TTL);
    }

    public function syncNivelesGrados(): bool {
        $response = $this->get(API_ENDPOINT_NIVELES_GRADOS);
        if (!$response) return false;

        $this->db->beginTransaction();
        try {
            foreach ($response['data'] as $nivel) {
                $this->db->prepare("
                    INSERT INTO niveles (id, nombre, api_sync_at) VALUES (?, ?, NOW())
                    ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), api_sync_at = NOW()
                ")->execute([$nivel['id'], $nivel['nombre']]);

                foreach ($nivel['grados'] as $grado) {
                    $this->db->prepare("
                        INSERT INTO grados (id, nivel_id, nombre, api_sync_at) VALUES (?, ?, ?, NOW())
                        ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), api_sync_at = NOW()
                    ")->execute([$grado['id'], $nivel['id'], $grado['nombre']]);
                }
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getAlumnosActivos(?string $nivel = null, ?string $grado = null): array {
        $response = $this->get(API_ENDPOINT_ALUMNOS);
        if (!$response) return [];

        return array_filter($response['data'], function ($alumno) use ($nivel, $grado) {
            if ($alumno['estado'] !== 'Matriculado e Inscrito') return false;
            if ($alumno['academico']['nivel'] === 'N/A') return false;
            if ($nivel && $alumno['academico']['nivel'] !== $nivel) return false;
            if ($grado && $alumno['academico']['grado'] !== $grado) return false;
            return true;
        });
    }

    public function isApiOnline(): bool {
        if (API_USE_MOCK) return true;
        $url = rtrim(API_BASE_URL, '/') . API_ENDPOINT_COLEGIO;
        $ctx = stream_context_create([
            'http' => [
                'timeout' => 2,
                'header'  => "Authorization: Bearer " . API_BEARER_TOKEN . "\r\n",
            ]
        ]);
        $raw = @file_get_contents($url, false, $ctx);
        return $raw !== false;
    }
}
