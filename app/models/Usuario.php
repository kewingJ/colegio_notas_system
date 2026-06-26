<?php
class Usuario extends Model {
    public function getAll(int $page = 1, int $perPage = 15, array $filters = []): array {
        $offset = ($page - 1) * $perPage;
        $params = [];

        $sql = "SELECT u.*, r.nombre as rol_nombre
                FROM usuarios u
                JOIN roles r ON u.rol_id = r.id
                WHERE 1=1";

        if (!empty($filters['search'])) {
            $sql .= " AND (u.nombre LIKE ? OR u.email LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }

        if (!empty($filters['rol_id'])) {
            $sql .= " AND u.rol_id = ?";
            $params[] = $filters['rol_id'];
        }

        if (isset($filters['activo']) && $filters['activo'] !== '') {
            $sql .= " AND u.activo = ?";
            $params[] = $filters['activo'];
        }

        $sql .= " ORDER BY u.created_at DESC LIMIT $perPage OFFSET $offset";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countAll(array $filters = []): int {
        $params = [];
        $sql = "SELECT COUNT(*) FROM usuarios u WHERE 1=1";

        if (!empty($filters['search'])) {
            $sql .= " AND (u.nombre LIKE ? OR u.email LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }

        if (!empty($filters['rol_id'])) {
            $sql .= " AND u.rol_id = ?";
            $params[] = $filters['rol_id'];
        }

        if (isset($filters['activo']) && $filters['activo'] !== '') {
            $sql .= " AND u.activo = ?";
            $params[] = $filters['activo'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT u.*, r.nombre as rol_nombre FROM usuarios u JOIN roles r ON u.rol_id = r.id WHERE u.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare("
            INSERT INTO usuarios (rol_id, nombre, email, password_hash, activo, telefono, especialidad, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['rol_id'],
            $data['nombre'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]),
            $data['activo'] ?? 1,
            $data['telefono'] ?? null,
            $data['especialidad'] ?? null,
            Session::get('user_id')
        ]);
    }

    public function update(int $id, array $data): bool {
        $fields = "rol_id = ?, nombre = ?, email = ?, activo = ?, telefono = ?, especialidad = ?";
        $params = [
            $data['rol_id'],
            $data['nombre'],
            $data['email'],
            $data['activo'],
            $data['telefono'] ?? null,
            $data['especialidad'] ?? null
        ];

        if (!empty($data['password'])) {
            $fields .= ", password_hash = ?";
            $params[] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
        }

        $params[] = $id;
        $stmt = $this->db->prepare("UPDATE usuarios SET $fields WHERE id = ?");
        return $stmt->execute($params);
    }

    public function delete(int $id): bool {
        // En vez de borrar, desactivamos por integridad referencial
        $stmt = $this->db->prepare("UPDATE usuarios SET activo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getRoles(): array {
        return $this->db->query("SELECT * FROM roles WHERE nombre IN ('administrador', 'profesor') ORDER BY nombre")->fetchAll();
    }
}
