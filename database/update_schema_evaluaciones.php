<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "Updating database schema for dynamic evaluations...\n";

    // 1. Table: materia_evaluaciones
    $db->exec("CREATE TABLE IF NOT EXISTS materia_evaluaciones (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        materia_id INT UNSIGNED NOT NULL,
        nombre VARCHAR(50) NOT NULL,
        peso_porcentaje DECIMAL(5,2) NOT NULL DEFAULT 0.00,
        activo TINYINT(1) DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_me_materia FOREIGN KEY (materia_id) REFERENCES materias(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");
    echo "Table 'materia_evaluaciones' created.\n";

    // 2. Add Unique constraint to materias (if not exists)
    // Warning: unique code already exists. User wants name + grade_id uniqueness.
    try {
        $db->exec("ALTER TABLE materias ADD UNIQUE KEY uq_nombre_grado (nombre, grado_id)");
        echo "Unique constraint added to 'materias' (nombre, grado_id).\n";
    } catch (Exception $e) {
        echo "Constraint already exists or could not be added: " . $e->getMessage() . "\n";
    }

    echo "Database schema update completed successfully.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
