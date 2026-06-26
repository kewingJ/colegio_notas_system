<?php
class Router {
    private array $routes = [];

    public function dispatch(): void {
        $url = $_GET['url'] ?? '';
        $url = rtrim($url, '/');

        // Mapeo simple de rutas basado en la estructura solicitada
        // /auth/login -> AuthController@login
        // /dashboard -> DashboardController@index
        // /usuarios -> UsuarioController@index
        // /usuarios/create -> UsuarioController@create

        $parts = explode('/', $url);
        $controllerName = !empty($parts[0]) ? ucfirst($parts[0]) . 'Controller' : 'DashboardController';
        $methodName = $parts[1] ?? 'index';

        if (!class_exists($controllerName)) {
            $this->error404("Controlador $controllerName no encontrado.");
            return;
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $methodName)) {
            $this->error404("Método $methodName no encontrado en $controllerName.");
            return;
        }

        // Pasar parámetros restantes
        $params = array_slice($parts, 2);
        call_user_func_array([$controller, $methodName], $params);
    }

    private function error404(string $message = ''): void {
        http_response_code(404);
        echo "<h1>404 Not Found</h1>";
        if (APP_ENV === 'development') {
            echo "<p>$message</p>";
        }
    }
}
