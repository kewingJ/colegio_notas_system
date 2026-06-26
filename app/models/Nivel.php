<?php
class Nivel extends Model {
    public function getAll(): array {
        return $this->db->query("SELECT * FROM niveles ORDER BY nombre")->fetchAll();
    }
}
