<?php
/**
 * SEEDER — USUARIO ADMIN POR DEFECTO Y CONFIGURACIÓN INICIAL
 * Ejecutar este archivo una sola vez después de importar el schema.sql.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "Iniciando Seeder...\n";

    // 1. Configuración inicial
    $configuraciones = [
        ['anio_lectivo_activo', date('Y')],
        ['ultima_sincronizacion_api', null]
    ];

    $stmtConfig = $db->prepare("INSERT INTO configuracion (clave, valor) VALUES (?, ?) ON DUPLICATE KEY UPDATE valor = VALUES(valor)");
    foreach ($configuraciones as $conf) {
        $stmtConfig->execute($conf);
    }
    echo "Configuración inicial insertada.\n";

    // 2. Usuario Administrador
    $email = 'admin@arcoiris.edu.ni';
    $password = 'Admin2025*';
    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);

    $stmtUser = $db->prepare("
        INSERT INTO usuarios (rol_id, nombre, email, password_hash, activo)
        VALUES (1, 'Administrador Principal', ?, ?, 1)
        ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)
    ");
    $stmtUser->execute([$email, $hash]);

    echo "--------------------------------------------------\n";
    echo "Seeder ejecutado con éxito.\n";
    echo "Usuario admin listo:\n";
    echo "Email: " . $email . "\n";
    echo "Password: " . $password . "\n";
    echo "--------------------------------------------------\n";
    echo "IMPORTANTE: Cambia la contraseña inmediatamente después de entrar.\n";

} catch (Exception $e) {
    echo "ERROR EN EL SEEDER: " . $e->getMessage() . "\n";
}
