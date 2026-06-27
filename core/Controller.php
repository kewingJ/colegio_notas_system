<?php
abstract class Controller {
    protected function view(string $path, array $data = []): void {
        extract($data);

        $viewFile = __DIR__ . '/../views/' . $path . '.php';
        if (!file_exists($viewFile)) {
            die("Vista no encontrada: $path");
        }

        require_once $viewFile;
    }

    protected function redirect(string $url): void {
        header("Location: " . APP_URL . '/' . ltrim($url, '/'));
        exit;
    }

    protected function json(array $data): void {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function getPost(): array {
        return $_POST;
    }

    protected function validateCsrf(): bool {
        if (!$this->isPost()) return true;

        $token = $_POST[CSRF_TOKEN_NAME] ?? '';
        if (empty($token) || $token !== Session::get(CSRF_TOKEN_NAME)) {
            $this->setFlash('error', 'Sesión inválida o expirada (CSRF).');
            $this->redirect('dashboard');
            return false;
        }
        return true;
    }

    protected function setFlash(string $type, string $message): void {
        Session::setFlash($type, $message);
    }

    protected function requireAuth(?string $role = null): void {
        if (!Session::get('user_id')) {
            $this->redirect('auth/login');
        }

        if ($role && Session::get('role_name') !== $role) {
            $this->setFlash('error', 'No tienes permisos para acceder a esta sección.');
            $this->redirect('dashboard');
        }
    }

    protected function logAudit(string $accion, ?string $tabla = null, ?int $registroId = null, $detalle = null): void {
        require_once __DIR__ . '/../app/models/Audit.php';
        $auditModel = new Audit();
        $usuarioId = Session::get('user_id');
        $auditModel->log($usuarioId, $accion, $tabla, $registroId, $detalle);
    }
}
