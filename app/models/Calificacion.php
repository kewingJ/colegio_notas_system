<?php
class Calificacion extends Model {
    public function save(int $inscripcionId, int $periodoId, float $nota, string $type = 'global', ?string $observaciones = null): bool {
        $stmt = $this->db->prepare("
            INSERT INTO calificaciones (inscripcion_id, periodo_id, period_type, nota, observaciones)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                nota = VALUES(nota),
                observaciones = VALUES(observaciones),
                updated_at = NOW()
        ");
        return $stmt->execute([$inscripcionId, $periodoId, $type, $nota, $observaciones]);
    }

    public function getByInscripcion(int $inscripcionId): array {
        $stmt = $this->db->prepare("
            SELECT c.*, p.nombre as periodo_nombre, 1 as is_global
            FROM calificaciones c
            JOIN periodos p ON c.periodo_id = p.id
            WHERE c.inscripcion_id = ? AND c.period_type = 'global'
            UNION
            SELECT c.*, me.nombre as periodo_nombre, 0 as is_global
            FROM calificaciones c
            JOIN materia_evaluaciones me ON c.periodo_id = me.id
            WHERE c.inscripcion_id = ? AND c.period_type = 'custom'
        ");
        $stmt->execute([$inscripcionId, $inscripcionId]);
        return $stmt->fetchAll();
    }

    public function getGradesMatrix(int $profesorMateriaId): array {
        // Devuelve una matriz de notas por alumno para una materia específica
        $stmt = $this->db->prepare("
            SELECT
                i.alumno_carnet,
                p.id as periodo_id,
                p.nombre as periodo_nombre,
                c.nota,
                i.id as inscripcion_id
            FROM inscripciones i
            CROSS JOIN periodos p
            LEFT JOIN calificaciones c ON c.inscripcion_id = i.id AND c.periodo_id = p.id
            WHERE i.profesor_materia_id = ? AND p.activo = 1
            ORDER BY i.alumno_carnet, p.id
        ");
        $stmt->execute([$profesorMateriaId]);
        return $stmt->fetchAll();
    }
}
