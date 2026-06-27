<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "Updating calificaciones table for dynamic period types...\n";

    // 1. Add period_type to calificaciones
    try {
        $db->exec("ALTER TABLE calificaciones ADD COLUMN period_type ENUM('global', 'custom') DEFAULT 'global' AFTER periodo_id");
        echo "Column 'period_type' added to 'calificaciones'.\n";
    } catch (Exception $e) {
        echo "Column already exists or could not be added: " . $e->getMessage() . "\n";
    }

    // 2. Drop and recreate Unique key to include period_type
    try {
        // Para evitar el error 1553 (índice necesario para FK),
        // primero creamos un índice normal que respalde la FK permanentemente
        $db->exec("ALTER TABLE calificaciones ADD INDEX idx_inscripcion_id (inscripcion_id)");
        echo "Supporting index 'idx_inscripcion_id' added for Foreign Key.\n";

        // Ahora intentamos añadir el nuevo UNIQUE
        $db->exec("ALTER TABLE calificaciones ADD UNIQUE KEY uq_insc_periodo_type (inscripcion_id, periodo_id, period_type)");
        echo "New unique constraint 'uq_insc_periodo_type' added.\n";

        // Y ahora podemos borrar el viejo sin problemas
        $db->exec("ALTER TABLE calificaciones DROP INDEX uq_insc_periodo");
        echo "Old unique constraint 'uq_insc_periodo' removed.\n";
    } catch (Exception $e) {
        echo "Note: " . $e->getMessage() . "\n";
    }

    echo "Database schema update completed successfully.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
