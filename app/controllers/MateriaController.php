<?php
class MateriaController extends Controller {
    private Materia $materiaModel;
    private ApiRestService $apiService;

    public function __construct() {
        $db = Database::getInstance()->getConnection();
        $this->materiaModel = new Materia();
        $this->apiService = new ApiRestService($db);
    }

    public function index(): void {
        $this->requireAuth();

        $page = (int)($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        $nivel_id = $_GET['nivel_id'] ?? '';
        $grado_id = $_GET['grado_id'] ?? '';

        $filters = ['search' => $search, 'nivel_id' => $nivel_id, 'grado_id' => $grado_id];
        $materias = $this->materiaModel->getAll($page, DEFAULT_PAGE_SIZE, $filters);
        $total = $this->materiaModel->countAll($filters);

        $niveles = $this->materiaModel->getNiveles();
        $grados = $nivel_id ? $this->materiaModel->getGrados($nivel_id) : [];

        // Conteo dinámico de alumnos inscritos (Simulado con API)
        $alumnos = $this->apiService->getAlumnosActivos();
        foreach ($materias as &$m) {
            $m['inscritos'] = count(array_filter($alumnos, function($a) use ($m) {
                return $a['academico']['nivel'] === $m['nivel_nombre'] &&
                       $a['academico']['grado'] === $m['grado_nombre'] &&
                       $a['academico']['seccion'] === ($m['seccion'] ?: 'A');
            }));
        }

        ob_start();
        extract([
            'materias' => $materias,
            'niveles' => $niveles,
            'grados' => $grados,
            'total' => $total,
            'page' => $page,
            'search' => $search,
            'nivel_id' => $nivel_id,
            'grado_id' => $grado_id,
            'perPage' => DEFAULT_PAGE_SIZE
        ]);
        require_once __DIR__ . '/../../views/materias/index.php';
        $content = ob_get_clean();

        $this->view('layouts/main', [
            'title' => 'Gestión de Materias',
            'subtitle' => 'Administra el currículo académico y asigna personal docente.',
            'content' => $content
        ]);
    }

    public function create(): void {
        $this->requireAuth('administrador');
        $niveles = $this->materiaModel->getNiveles();

        ob_start();
        require_once __DIR__ . '/../../views/materias/create.php';
        $content = ob_get_clean();

        $this->view('layouts/main', [
            'title' => 'Nueva Materia',
            'content' => $content
        ]);
    }

    public function store(): void {
        $this->requireAuth('administrador');
        $this->validateCsrf();
        $data = $this->getPost();

        if ($this->materiaModel->create($data)) {
            $this->setFlash('success', 'Materia creada correctamente.');
            $this->redirect('materias');
        } else {
            $this->setFlash('error', 'Error al crear materia.');
            $this->redirect('materias/create');
        }
    }

    public function edit($id): void {
        $this->requireAuth('administrador');
        $materia = $this->materiaModel->findById((int)$id);
        $niveles = $this->materiaModel->getNiveles();
        $grados = $this->materiaModel->getGrados($materia['nivel_id']);

        ob_start();
        require_once __DIR__ . '/../../views/materias/edit.php';
        $content = ob_get_clean();

        $this->view('layouts/main', [
            'title' => 'Editar Materia',
            'content' => $content
        ]);
    }

    public function update($id): void {
        $this->requireAuth('administrador');
        $this->validateCsrf();
        $data = $this->getPost();

        if ($this->materiaModel->update((int)$id, $data)) {
            $this->setFlash('success', 'Materia actualizada correctamente.');
            $this->redirect('materias');
        } else {
            $this->setFlash('error', 'Error al actualizar.');
            $this->redirect("materias/edit/$id");
        }
    }

    public function assign($id): void {
        $this->requireAuth('administrador');
        $materia = $this->materiaModel->findById((int)$id);

        $db = Database::getInstance()->getConnection();
        $stmtProf = $db->prepare("SELECT id, nombre FROM usuarios WHERE rol_id = (SELECT id FROM roles WHERE nombre = 'profesor') AND activo = 1");
        $stmtProf->execute();
        $profesores = $stmtProf->fetchAll();

        $stmtConfig = $db->prepare("SELECT valor FROM configuracion WHERE clave = 'anio_lectivo_activo'");
        $stmtConfig->execute();
        $anioActivo = $stmtConfig->fetchColumn();

        ob_start();
        require_once __DIR__ . '/../../views/materias/assign.php';
        $content = ob_get_clean();

        $this->view('layouts/main', [
            'title' => 'Asignar Profesor',
            'content' => $content
        ]);
    }

    public function doAssign($id): void {
        $this->requireAuth('administrador');
        $this->validateCsrf();
        $data = $this->getPost();

        if ($this->materiaModel->assignProfessor((int)$id, $data['profesor_id'], $data['anio_lectivo'], $data['seccion'])) {
            $this->setFlash('success', 'Profesor asignado correctamente.');
            $this->redirect('materias');
        } else {
            $this->setFlash('error', 'Error en la asignación.');
            $this->redirect("materias/assign/$id");
        }
    }

    /**
     * API Endpoint para obtener grados por nivel (AJAX)
     */
    public function apiGrados($nivelId): void {
        $grados = $this->materiaModel->getGrados((int)$nivelId);
        $this->json($grados);
    }
}
