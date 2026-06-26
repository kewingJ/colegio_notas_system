<?php
abstract class Model {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Helper para obtener configuración global de la tabla `configuracion`
     */
    public function getConfig(string $clave): ?string {
        $stmt = $this->db->prepare("SELECT valor FROM configuracion WHERE clave = ?");
        $stmt->execute([$clave]);
        return $stmt->fetchColumn() ?: null;
    }

    /**
     * Helper para guardar configuración global
     */
    public function setConfig(string $clave, string $valor): void {
        $stmt = $this->db->prepare("INSERT INTO configuracion (clave, valor) VALUES (?, ?) ON DUPLICATE KEY UPDATE valor = VALUES(valor)");
        $stmt->execute([$clave, $valor]);
    }
}
