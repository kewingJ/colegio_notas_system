<?php
class DashboardController extends Controller {
    private PDO $db;
    private ApiRestService $apiService;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->apiService = new ApiRestService($this->db);
    }

    public function index(): void {
        $this->requireAuth();

        // 1. Obtener Año Lectivo Activo
        $stmtAnio = $this->db->prepare("SELECT valor FROM configuracion WHERE clave = 'anio_lectivo_activo'");
        $stmtAnio->execute();
        $anioActivo = $stmtAnio->fetchColumn() ?: date('Y');

        // 2. KPIs
        // Profesores activos
        $stmtProf = $this->db->prepare("SELECT COUNT(*) FROM usuarios WHERE rol_id = (SELECT id FROM roles WHERE nombre = 'profesor') AND activo = 1");
        $stmtProf->execute();
        $totalProfesores = $stmtProf->fetchColumn();

        // Materias registradas
        $stmtMat = $this->db->prepare("SELECT COUNT(*) FROM materias WHERE activa = 1");
        $stmtMat->execute();
        $totalMaterias = $stmtMat->fetchColumn();

        // Horarios configurados
        $stmtHor = $this->db->prepare("SELECT COUNT(*) FROM horarios WHERE anio_lectivo = ? AND activo = 1");
        $stmtHor->execute([$anioActivo]);
        $totalHorarios = $stmtHor->fetchColumn();

        // Alumnos (desde API)
        $alumnos = $this->apiService->getAlumnosActivos();
        $totalAlumnos = count($alumnos);

        // 3. Profesores recientes
        $stmtRecientes = $this->db->prepare("
            SELECT u.*, r.nombre as rol_nombre
            FROM usuarios u
            JOIN roles r ON u.rol_id = r.id
            ORDER BY u.created_at DESC LIMIT 5
        ");
        $stmtRecientes->execute();
        $usuariosRecientes = $stmtRecientes->fetchAll();

        // 4. Estado API
        $apiOnline = $this->apiService->isApiOnline();
        $ultimaSync = $this->db->prepare("SELECT valor FROM configuracion WHERE clave = 'ultima_sincronizacion_api'");
        $ultimaSync->execute();
        $fechaSync = $ultimaSync->fetchColumn();

        ob_start();
        $data = [
            'anioActivo' => $anioActivo,
            'totalProfesores' => $totalProfesores,
            'totalMaterias' => $totalMaterias,
            'totalHorarios' => $totalHorarios,
            'totalAlumnos' => $totalAlumnos,
            'usuariosRecientes' => $usuariosRecientes,
            'apiOnline' => $apiOnline,
            'fechaSync' => $fechaSync
        ];
        extract($data);
        require_once __DIR__ . '/../../views/dashboard/index.php';
        $content = ob_get_clean();

        $this->view('layouts/main', [
            'title' => 'Panel Principal',
            'subtitle' => 'Bienvenido al sistema de gestión académica',
            'content' => $content,
            'apiOffline' => !$apiOnline
        ]);
    }

    public function updateAnioLectivo(): void {
        $this->requireAuth('administrador');
        $this->validateCsrf();

        $anio = $_POST['anio_lectivo'] ?? date('Y');
        $stmt = $this->db->prepare("INSERT INTO configuracion (clave, valor) VALUES ('anio_lectivo_activo', ?) ON DUPLICATE KEY UPDATE valor = VALUES(valor)");
        $stmt->execute([$anio]);

        $this->setFlash('success', 'Año lectivo actualizado correctamente.');
        $this->redirect('dashboard');
    }
}
