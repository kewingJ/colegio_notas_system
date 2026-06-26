<?php
class AuthService {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function login(string $email, string $password): bool {
        // Bloqueo de intentos (simplemente en sesión para esta fase)
        $attempts = Session::get('login_attempts') ?? 0;
        $lastAttempt = Session::get('last_attempt_time') ?? 0;

        if ($attempts >= 5 && (time() - $lastAttempt) < 300) {
            throw new Exception("Demasiados intentos fallidos. Intente en 5 minutos.");
        }

        $stmt = $this->db->prepare("
            SELECT u.*, r.nombre as role_name
            FROM usuarios u
            JOIN roles r ON u.rol_id = r.id
            WHERE u.email = ? AND u.activo = 1
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Éxito
            Session::set('user_id', $user['id']);
            Session::set('user_name', $user['nombre']);
            Session::set('role_name', $user['role_name']);
            Session::remove('login_attempts');

            $this->db->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?")->execute([$user['id']]);
            return true;
        }

        // Fallo
        Session::set('login_attempts', $attempts + 1);
        Session::set('last_attempt_time', time());
        return false;
    }

    public function logout(): void {
        Session::destroy();
    }
}
