<?php
class Materia extends Model {
    public function getAll(int $page = 1, int $perPage = 15, array $filters = []): array {
        $offset = ($page - 1) * $perPage;
        $params = [];

        $sql = "SELECT m.id, m.nombre, m.codigo, m.nivel_id, m.grado_id, m.cupo_maximo,
                       n.nombre as nivel_nombre, g.nombre as grado_nombre
                FROM materias m
                JOIN niveles n ON m.nivel_id = n.id
                JOIN grados g ON m.grado_id = g.id
                WHERE m.activa = 1";

        if (!empty($filters['search'])) {
            $sql .= " AND (m.nombre LIKE ? OR m.codigo LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }

        if (!empty($filters['nivel_id'])) {
            $sql .= " AND m.nivel_id = ?";
            $params[] = $filters['nivel_id'];
        }

        if (!empty($filters['grado_id'])) {
            $sql .= " AND m.grado_id = ?";
            $params[] = $filters['grado_id'];
        }

        $sql .= " ORDER BY m.id DESC LIMIT $perPage OFFSET $offset";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countAll(array $filters = []): int {
        $params = [];
        $sql = "SELECT COUNT(*) FROM materias m WHERE m.activa = 1";

        if (!empty($filters['search'])) {
            $sql .= " AND (m.nombre LIKE ? OR m.codigo LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }

        if (!empty($filters['nivel_id'])) {
            $sql .= " AND m.nivel_id = ?";
            $params[] = $filters['nivel_id'];
        }

        if (!empty($filters['grado_id'])) {
            $sql .= " AND m.grado_id = ?";
            $params[] = $filters['grado_id'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT m.*, n.nombre as nivel_nombre, g.nombre as grado_nombre
            FROM materias m
            JOIN niveles n ON m.nivel_id = n.id
            JOIN grados g ON m.grado_id = g.id
            WHERE m.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getAssignments(int $materiaId): array {
        $stmt = $this->db->prepare("
            SELECT pm.*, u.nombre as profesor_nombre
            FROM profesor_materia pm
            JOIN usuarios u ON pm.profesor_id = u.id
            WHERE pm.materia_id = ? AND pm.activo = 1
        ");
        $stmt->execute([$materiaId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare("
            INSERT INTO materias (nombre, codigo, nivel_id, grado_id, descripcion, cupo_maximo, activa, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['nombre'],
            $data['codigo'],
            $data['nivel_id'],
            $data['grado_id'],
            $data['descripcion'] ?? null,
            $data['cupo_maximo'] ?? 0,
            $data['activa'] ?? 1,
            Session::get('user_id')
        ]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE materias
            SET nombre = ?, codigo = ?, nivel_id = ?, grado_id = ?, descripcion = ?, cupo_maximo = ?, activa = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['nombre'],
            $data['codigo'],
            $data['nivel_id'],
            $data['grado_id'],
            $data['descripcion'] ?? null,
            $data['cupo_maximo'] ?? 0,
            $data['activa'],
            $id
        ]);
    }

    public function getNiveles(): array {
        return $this->db->query("SELECT * FROM niveles ORDER BY nombre")->fetchAll();
    }

    public function getGrados(?int $nivelId = null): array {
        if ($nivelId) {
            $stmt = $this->db->prepare("SELECT * FROM grados WHERE nivel_id = ? ORDER BY id");
            $stmt->execute([$nivelId]);
            return $stmt->fetchAll();
        }
        return $this->db->query("SELECT * FROM grados ORDER BY id")->fetchAll();
    }

    public function assignProfessor(int $materiaId, int $professorId, string $anio, string $seccion): bool {
        // Verificar si ya existe esta asignación exacta (materia, año, sección) para CUALQUIER profesor
        $stmtCheck = $this->db->prepare("SELECT id FROM profesor_materia WHERE materia_id = ? AND anio_lectivo = ? AND seccion = ? AND activo = 1");
        $stmtCheck->execute([$materiaId, $anio, $seccion]);
        $existing = $stmtCheck->fetch();

        if ($existing) {
            // Actualizar el profesor asignado a esa sección
            $stmt = $this->db->prepare("UPDATE profesor_materia SET profesor_id = ? WHERE id = ?");
            return $stmt->execute([$professorId, $existing['id']]);
        }

        $stmt = $this->db->prepare("
            INSERT INTO profesor_materia (profesor_id, materia_id, anio_lectivo, seccion, activo)
            VALUES (?, ?, ?, ?, 1)
        ");
        return $stmt->execute([$professorId, $materiaId, $anio, $seccion]);
    }

    public function delete(int $id): bool {
        // Soft delete: marcar como inactiva
        $stmt = $this->db->prepare("UPDATE materias SET activa = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getEvaluaciones(int $materiaId): array {
        $stmt = $this->db->prepare("SELECT * FROM materia_evaluaciones WHERE materia_id = ? AND activo = 1 ORDER BY id");
        $stmt->execute([$materiaId]);
        return $stmt->fetchAll();
    }

    public function setEvaluaciones(int $materiaId, array $evaluaciones): void {
        $this->db->beginTransaction();
        try {
            // Desactivar anteriores
            $stmtDel = $this->db->prepare("DELETE FROM materia_evaluaciones WHERE materia_id = ?");
            $stmtDel->execute([$materiaId]);

            $stmtIns = $this->db->prepare("INSERT INTO materia_evaluaciones (materia_id, nombre, peso_porcentaje) VALUES (?, ?, ?)");
            foreach ($evaluaciones as $eval) {
                if (!empty($eval['nombre'])) {
                    $stmtIns->execute([$materiaId, $eval['nombre'], $eval['peso']]);
                }
            }
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function generateSuggestedCode(string $name): string {
        $prefix = strtoupper(substr($name, 0, 3));
        if (strlen($prefix) < 3) {
            $prefix = str_pad($prefix, 3, 'X');
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM materias WHERE codigo LIKE ?");
        $stmt->execute(["$prefix-%"]);
        $count = (int)$stmt->fetchColumn();

        return "$prefix-" . ($count + 1);
    }
}
