<?php
class ApiCache extends Model {
    public function get(string $endpoint, bool $ignoreExpiry = false): ?array {
        $sql = $ignoreExpiry
            ? "SELECT data_json FROM api_cache WHERE endpoint = ? ORDER BY fetched_at DESC LIMIT 1"
            : "SELECT data_json FROM api_cache WHERE endpoint = ? AND expires_at > NOW() ORDER BY fetched_at DESC LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$endpoint]);
        $row = $stmt->fetch();

        return $row ? json_decode($row['data_json'], true) : null;
    }

    public function set(string $endpoint, array $data, int $ttl): void {
        $this->db->prepare("DELETE FROM api_cache WHERE endpoint = ?")->execute([$endpoint]);
        $stmt = $this->db->prepare("
            INSERT INTO api_cache (endpoint, data_json, fetched_at, expires_at)
            VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? SECOND))
        ");
        $stmt->execute([$endpoint, json_encode($data), $ttl]);
    }
}
