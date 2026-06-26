<?php
class AuthController extends Controller {
    private AuthService $authService;

    public function __construct() {
        $this->authService = new AuthService(Database::getInstance()->getConnection());
    }

    public function login(): void {
        if (Session::get('user_id')) {
            $this->redirect('dashboard');
        }

        ob_start();
        require_once __DIR__ . '/../../views/auth/login.php';
        $content = ob_get_clean();

        $this->view('layouts/auth', [
            'title' => 'Iniciar Sesión',
            'content' => $content
        ]);
    }

    public function doLogin(): void {
        if (!$this->isPost() || !$this->validateCsrf()) {
            $this->redirect('auth/login');
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        try {
            if ($this->authService->login($email, $password)) {
                $this->setFlash('success', 'Bienvenido al sistema.');
                $this->redirect('dashboard');
            } else {
                $this->setFlash('error', 'Credenciales incorrectas.');
                $this->redirect('auth/login');
            }
        } catch (Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->redirect('auth/login');
        }
    }

    public function logout(): void {
        $this->authService->logout();
        $this->redirect('auth/login');
    }
}
