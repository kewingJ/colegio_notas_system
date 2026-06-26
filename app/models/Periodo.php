<?php
class Periodo extends Model {
    public function getAllActive(): array {
        $stmt = $this->db->query("SELECT * FROM periodos WHERE activo = 1 ORDER BY id");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM periodos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
}
