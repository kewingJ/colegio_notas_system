<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "Updating database schema...\n";

    // 1. Table: periodos
    $db->exec("CREATE TABLE IF NOT EXISTS periodos (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(50) NOT NULL,
        activo TINYINT(1) DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;");
    echo "Table 'periodos' created or already exists.\n";

    // 2. Table: inscripciones
    $db->exec("CREATE TABLE IF NOT EXISTS inscripciones (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        alumno_carnet VARCHAR(20) NOT NULL,
        profesor_materia_id INT UNSIGNED NOT NULL,
        anio_lectivo YEAR NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_insc_pm FOREIGN KEY (profesor_materia_id) REFERENCES profesor_materia(id),
        INDEX idx_alumno_carnet (alumno_carnet),
        INDEX idx_anio (anio_lectivo)
    ) ENGINE=InnoDB;");
    echo "Table 'inscripciones' created or already exists.\n";

    // 3. Table: calificaciones
    $db->exec("CREATE TABLE IF NOT EXISTS calificaciones (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        inscripcion_id INT UNSIGNED NOT NULL,
        periodo_id INT UNSIGNED NOT NULL,
        nota DECIMAL(5,2) DEFAULT 0.00,
        observaciones TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT fk_calif_insc FOREIGN KEY (inscripcion_id) REFERENCES inscripciones(id),
        CONSTRAINT fk_calif_periodo FOREIGN KEY (periodo_id) REFERENCES periodos(id),
        UNIQUE KEY uq_insc_periodo (inscripcion_id, periodo_id)
    ) ENGINE=InnoDB;");
    echo "Table 'calificaciones' created or already exists.\n";

    // Seed initial periods if empty
    $stmtCount = $db->query("SELECT COUNT(*) FROM periodos");
    if ($stmtCount->fetchColumn() == 0) {
        $stmt = $db->prepare("INSERT INTO periodos (nombre) VALUES (?)");
        $periods = ['I Parcial', 'II Parcial', 'Examen'];
        foreach ($periods as $p) {
            $stmt->execute([$p]);
        }
        echo "Initial periods seeded.\n";
    }

    echo "Database schema update completed successfully.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
