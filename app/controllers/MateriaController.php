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

        // Estadísticas Reales
        $db = Database::getInstance()->getConnection();
        $totalCupos = (int)$db->query("SELECT SUM(cupo_maximo) FROM materias WHERE activa = 1")->fetchColumn();

        $inscripcionModel = new Inscripcion();

        foreach ($materias as &$m) {
            // Obtener el ID de la asignación (profesor_materia) para esta sección 'A' por defecto si no hay otra
            // En un sistema real, esto debería estar más integrado en el Model
            $stmtPM = $db->prepare("SELECT id FROM profesor_materia WHERE materia_id = ? AND activo = 1 LIMIT 1");
            $stmtPM->execute([$m['id']]);
            $pm_id = $stmtPM->fetchColumn();

            $m['pm_id'] = $pm_id;
            $m['inscritos'] = $pm_id ? count($inscripcionModel->getInscritosByMateria($pm_id)) : 0;
        }

        $totalInscritos = (int)$db->query("SELECT COUNT(*) FROM inscripciones")->fetchColumn();
        $cuposLibres = $totalCupos - $totalInscritos;
        $eficiencia = $totalCupos > 0 ? round(($totalInscritos / $totalCupos) * 100, 1) : 0;

        ob_start();
        extract([
            'materias' => $materias,
            'niveles' => $niveles,
            'grados' => $grados,
            'total' => $total,
            'totalCupos' => $totalCupos,
            'cuposLibres' => $cuposLibres,
            'eficiencia' => $eficiencia,
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

    public function enroll($materiaId): void {
        $this->requireAuth('administrador');

        $db = Database::getInstance()->getConnection();
        $stmtPM = $db->prepare("
            SELECT pm.*, m.nombre as materia_nombre, m.nivel_id, m.grado_id, n.nombre as nivel_nombre, g.nombre as grado_nombre
            FROM profesor_materia pm
            JOIN materias m ON pm.materia_id = m.id
            JOIN niveles n ON m.nivel_id = n.id
            JOIN grados g ON m.grado_id = g.id
            WHERE m.id = ? AND pm.activo = 1 LIMIT 1
        ");
        $stmtPM->execute([$materiaId]);
        $pm = $stmtPM->fetch();

        if (!$pm) {
            $this->setFlash('error', 'Debe asignar un profesor a la materia antes de inscribir alumnos.');
            $this->redirect('materias');
        }

        // Obtener alumnos de la API que coincidan con el nivel y grado
        $alumnos = $this->apiService->getAlumnosActivos($pm['nivel_nombre'], $pm['grado_nombre']);

        // Filtrar los que ya están inscritos
        $inscripcionModel = new Inscripcion();
        foreach ($alumnos as &$a) {
            $a['ya_inscrito'] = $inscripcionModel->isEnrolled($a['carnet'], $pm['id'], $pm['anio_lectivo']);
        }

        ob_start();
        extract(['pm' => $pm, 'alumnos' => $alumnos]);
        require_once __DIR__ . '/../../views/materias/enroll.php';
        $content = ob_get_clean();

        $this->view('layouts/main', [
            'title' => 'Inscribir Alumnos',
            'content' => $content
        ]);
    }

    public function doEnroll(): void {
        $this->requireAuth('administrador');
        $this->validateCsrf();
        $data = $this->getPost();

        $pm_id = (int)$data['pm_id'];
        $carnets = $data['alumnos'] ?? [];
        $anio = $data['anio_lectivo'];

        $inscripcionModel = new Inscripcion();
        $count = 0;
        foreach ($carnets as $carnet) {
            if (!$inscripcionModel->isEnrolled($carnet, $pm_id, $anio)) {
                if ($inscripcionModel->enroll($carnet, $pm_id, $anio)) {
                    $count++;
                }
            }
        }

        $this->setFlash('success', "$count alumnos inscritos correctamente.");
        $this->redirect('materias');
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
