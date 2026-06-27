<?php
class Audit extends Model {
    
    /**
     * Registra una acción en el log de auditoría
     * 
     * @param int|null $usuarioId ID del usuario que realiza la acción (puede ser null si es el sistema o login fallido)
     * @param string $accion Descripción breve de la acción (ej: 'CREAR_USUARIO')
     * @param string|null $tabla Nombre de la tabla afectada
     * @param int|null $registroId ID del registro afectado
     * @param mixed $detalle Arreglo u objeto con detalles adicionales de la acción (se guardará como JSON)
     * @return bool
     */
    public function log(?int $usuarioId, string $accion, ?string $tabla = null, ?int $registroId = null, $detalle = null): bool {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $detalleJson = $detalle ? json_encode($detalle, JSON_UNESCAPED_UNICODE) : null;
        
        $stmt = $this->db->prepare("
            INSERT INTO audit_log (usuario_id, accion, tabla, registro_id, detalle, ip) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([$usuarioId, $accion, $tabla, $registroId, $detalleJson, $ip]);
    }
}
