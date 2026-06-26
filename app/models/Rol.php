<?php
class Rol extends Model {
    public function getAll(): array {
        return $this->db->query("SELECT * FROM roles ORDER BY nombre")->fetchAll();
    }

    public function getAdminAndProfesor(): array {
        return $this->db->query("SELECT * FROM roles WHERE nombre IN ('administrador', 'profesor') ORDER BY nombre")->fetchAll();
    }
}
