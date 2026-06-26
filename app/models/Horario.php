<?php
class Horario extends Model {
    public function getByProfesor(int $profesorId, string $anio): array {
        $stmt = $this->db->prepare("
            SELECT h.*, m.nombre as materia_nombre, m.codigo as materia_codigo
            FROM horarios h
            JOIN materias m ON h.materia_id = m.id
            WHERE h.profesor_id = ? AND h.anio_lectivo = ? AND h.activo = 1
            ORDER BY h.dia_semana, h.hora_inicio
        ");
        $stmt->execute([$profesorId, $anio]);
        return $stmt->fetchAll();
    }

    public function getMateriasAsignadas(int $profesorId, string $anio): array {
        $stmt = $this->db->prepare("
            SELECT m.id, m.nombre, m.codigo, pm.seccion
            FROM materias m
            JOIN profesor_materia pm ON m.id = pm.materia_id
            WHERE pm.profesor_id = ? AND pm.anio_lectivo = ? AND pm.activo = 1
        ");
        $stmt->execute([$profesorId, $anio]);
        return $stmt->fetchAll();
    }

    public function checkOverlap(int $profesorId, int $dia, string $inicio, string $fin, string $anio, ?int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) FROM horarios
                WHERE profesor_id = ? AND dia_semana = ? AND anio_lectivo = ? AND activo = 1
                AND ((hora_inicio < ? AND hora_fin > ?) OR (hora_inicio >= ? AND hora_inicio < ?))";

        $params = [$profesorId, $dia, $anio, $fin, $inicio, $inicio, $fin];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare("
            INSERT INTO horarios (profesor_id, materia_id, dia_semana, hora_inicio, hora_fin, aula, anio_lectivo, seccion, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['profesor_id'],
            $data['materia_id'],
            $data['dia_semana'],
            $data['hora_inicio'],
            $data['hora_fin'],
            $data['aula'] ?? null,
            $data['anio_lectivo'],
            $data['seccion'],
            Session::get('user_id')
        ]);
    }

    public function delete(int $id): bool {
        return $this->db->prepare("UPDATE horarios SET activo = 0 WHERE id = ?")->execute([$id]);
    }
}
