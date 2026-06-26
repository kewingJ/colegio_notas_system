<?php
class Grado extends Model {
    public function getByNivel(int $nivelId): array {
        $stmt = $this->db->prepare("SELECT * FROM grados WHERE nivel_id = ? ORDER BY id");
        $stmt->execute([$nivelId]);
        return $stmt->fetchAll();
    }
}
