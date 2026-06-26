<?php
class Materia extends Model {
    public function getAll(int $page = 1, int $perPage = 15, array $filters = []): array {
        $offset = ($page - 1) * $perPage;
        $params = [];

        $sql = "SELECT m.*, n.nombre as nivel_nombre, g.nombre as grado_nombre,
                       (SELECT u.nombre FROM profesor_materia pm
                        JOIN usuarios u ON pm.profesor_id = u.id
                        WHERE pm.materia_id = m.id AND pm.activo = 1 LIMIT 1) as profesor_nombre,
                       (SELECT pm.seccion FROM profesor_materia pm WHERE pm.materia_id = m.id AND pm.activo = 1 LIMIT 1) as seccion
                FROM materias m
                JOIN niveles n ON m.nivel_id = n.id
                JOIN grados g ON m.grado_id = g.id
                WHERE 1=1";

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
        $sql = "SELECT COUNT(*) FROM materias m WHERE 1=1";

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
        $stmt = $this->db->prepare("SELECT * FROM materias WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
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
        $stmt = $this->db->prepare("
            INSERT INTO profesor_materia (profesor_id, materia_id, anio_lectivo, seccion, activo)
            VALUES (?, ?, ?, ?, 1)
            ON DUPLICATE KEY UPDATE profesor_id = VALUES(profesor_id), activo = 1
        ");
        return $stmt->execute([$professorId, $materiaId, $anio, $seccion]);
    }
}
