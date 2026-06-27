<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "Fixing calificaciones foreign key...\n";

    // Intentar eliminar la clave foránea fk_calif_periodo
    try {
        $db->exec("ALTER TABLE calificaciones DROP FOREIGN KEY fk_calif_periodo");
        echo "Successfully dropped foreign key 'fk_calif_periodo'.\n";
    } catch (Exception $e) {
        echo "Note: Foreign key 'fk_calif_periodo' might already be dropped or an error occurred: " . $e->getMessage() . "\n";
    }

    echo "Fix completed.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
