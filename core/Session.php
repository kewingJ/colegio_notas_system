<?php
class Session {
    public static function init(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_set_cookie_params([
                'lifetime' => SESSION_LIFETIME,
                'path'     => '/',
                'secure'   => false, // true en prod
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
            session_start();
        }

        // Generar CSRF token si no existe
        if (!self::get(CSRF_TOKEN_NAME)) {
            self::set(CSRF_TOKEN_NAME, bin2hex(random_bytes(32)));
        }
    }

    public static function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key): mixed {
        return $_SESSION[$key] ?? null;
    }

    public static function remove(string $key): void {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void {
        session_unset();
        session_destroy();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httonly"]
            );
        }
    }

    public static function setFlash(string $type, string $message): void {
        $_SESSION['flash'][$type] = $message;
    }

    public static function getFlash(): array {
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flash;
    }
}
