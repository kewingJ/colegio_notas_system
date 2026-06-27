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

    public function unenroll(string $carnet, int $profesorMateriaId, string $anio): bool {
        try {
            $this->db->beginTransaction();

            // Obtener el ID de la inscripción
            $stmt = $this->db->prepare("SELECT id FROM inscripciones WHERE alumno_carnet = ? AND profesor_materia_id = ? AND anio_lectivo = ?");
            $stmt->execute([$carnet, $profesorMateriaId, $anio]);
            $inscripcionId = $stmt->fetchColumn();

            if ($inscripcionId) {
                // Eliminar calificaciones asociadas
                $stmtDelNotas = $this->db->prepare("DELETE FROM calificaciones WHERE inscripcion_id = ?");
                $stmtDelNotas->execute([$inscripcionId]);

                // Eliminar la inscripción
                $stmtDelInsc = $this->db->prepare("DELETE FROM inscripciones WHERE id = ?");
                $stmtDelInsc->execute([$inscripcionId]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
