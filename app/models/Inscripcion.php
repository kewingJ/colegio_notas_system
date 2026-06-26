<?php
class Inscripcion extends Model {
    public function enroll(string $carnet, int $profesorMateriaId, string $anio): bool {
        $stmt = $this->db->prepare("
            INSERT INTO inscripciones (alumno_carnet, profesor_materia_id, anio_lectivo)
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$carnet, $profesorMateriaId, $anio]);
    }

    public function isEnrolled(string $carnet, int $profesorMateriaId, string $anio): bool {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM inscripciones
            WHERE alumno_carnet = ? AND profesor_materia_id = ? AND anio_lectivo = ?
        ");
        $stmt->execute([$carnet, $profesorMateriaId, $anio]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function getInscritosByMateria(int $profesorMateriaId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM inscripciones WHERE profesor_materia_id = ?
        ");
        $stmt->execute([$profesorMateriaId]);
        return $stmt->fetchAll();
    }
}
