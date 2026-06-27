<?php
class MateriasController extends Controller {
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

        foreach ($materias as $key => $m) {
            $assignments = $this->materiaModel->getAssignments($m['id']);
            $materias[$key]['assignments'] = $assignments;
            $materias[$key]['inscritos_total'] = 0;
            foreach ($assignments as $asig) {
                $materias[$key]['inscritos_total'] += count($inscripcionModel->getInscritosByMateria($asig['id']));
            }
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

        $page = (int)($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        $pm_id_filter = $_GET['pm_id'] ?? null;
        $perPage = 10;

        $db = Database::getInstance()->getConnection();

        // Si viene un pm_id específico, usarlo. Si no, intentar obtener el primero.
        if ($pm_id_filter) {
            $stmtPM = $db->prepare("
                SELECT pm.*, m.nombre as materia_nombre, m.nivel_id, m.grado_id, n.nombre as nivel_nombre, g.nombre as grado_nombre
                FROM profesor_materia pm
                JOIN materias m ON pm.materia_id = m.id
                JOIN niveles n ON m.nivel_id = n.id
                JOIN grados g ON m.grado_id = g.id
                WHERE pm.id = ? AND pm.activo = 1
            ");
            $stmtPM->execute([$pm_id_filter]);
        } else {
            $stmtPM = $db->prepare("
                SELECT pm.*, m.nombre as materia_nombre, m.nivel_id, m.grado_id, n.nombre as nivel_nombre, g.nombre as grado_nombre
                FROM profesor_materia pm
                JOIN materias m ON pm.materia_id = m.id
                JOIN niveles n ON m.nivel_id = n.id
                JOIN grados g ON m.grado_id = g.id
                WHERE m.id = ? AND pm.activo = 1 LIMIT 1
            ");
            $stmtPM->execute([$materiaId]);
        }

        $pm = $stmtPM->fetch();

        if (!$pm) {
            $this->setFlash('error', 'Debe asignar un profesor y sección a la materia antes de inscribir alumnos.');
            $this->redirect('materias');
        }

        // Obtener alumnos de la API que coincidan con el nivel y grado
        $todosAlumnos = $this->apiService->getAlumnosActivos($pm['nivel_nombre'], $pm['grado_nombre']);

        // Filtrar y Procesar
        $alumnosProcesados = [];
        $inscripcionModel = new Inscripcion();
        foreach ($todosAlumnos as $a) {
            if (!empty($search) && stripos($a['nombre'], $search) === false && stripos($a['carnet'], $search) === false) {
                continue;
            }

            $a['ya_inscrito'] = $inscripcionModel->isEnrolled($a['carnet'], $pm['id'], $pm['anio_lectivo']);
            $alumnosProcesados[] = $a;
        }

        $total = count($alumnosProcesados);
        $offset = ($page - 1) * $perPage;
        $alumnos = array_slice($alumnosProcesados, $offset, $perPage);

        ob_start();
        extract([
            'pm' => $pm,
            'alumnos' => $alumnos,
            'total' => $total,
            'page' => $page,
            'search' => $search,
            'perPage' => $perPage
        ]);
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
        $anio = $data['anio_lectivo'];
        $inscripcionModel = new Inscripcion();
        $count = 0;

        // Caso 1: Seleccionar todos los resultados filtrados
        if (isset($data['select_all_results']) && $data['select_all_results'] == '1') {
            // Re-obtener los datos de la materia para conocer nivel/grado
            $db = Database::getInstance()->getConnection();
            $stmtPM = $db->prepare("
                SELECT m.nombre as materia_nombre, n.nombre as nivel_nombre, g.nombre as grado_nombre
                FROM profesor_materia pm
                JOIN materias m ON pm.materia_id = m.id
                JOIN niveles n ON m.nivel_id = n.id
                JOIN grados g ON m.grado_id = g.id
                WHERE pm.id = ?
            ");
            $stmtPM->execute([$pm_id]);
            $pm_info = $stmtPM->fetch();

            // Obtener todos los alumnos del sistema que coincidan con el nivel/grado
            $alumnos = $this->apiService->getAlumnosActivos($pm_info['nivel_nombre'], $pm_info['grado_nombre']);
            foreach ($alumnos as $a) {
                if (!$inscripcionModel->isEnrolled($a['carnet'], $pm_id, $anio)) {
                    if ($inscripcionModel->enroll($a['carnet'], $pm_id, $anio)) {
                        $count++;
                    }
                }
            }
        }
        // Caso 2: Selección manual individual (acumulada por JS o normal)
        else {
            $carnets = $data['alumnos'] ?? [];
            foreach ($carnets as $carnet) {
                if (!$inscripcionModel->isEnrolled($carnet, $pm_id, $anio)) {
                    if ($inscripcionModel->enroll($carnet, $pm_id, $anio)) {
                        $count++;
                    }
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

        // Check for duplicates
        $db = Database::getInstance()->getConnection();
        $stmtCheck = $db->prepare("SELECT id FROM materias WHERE nombre = ? AND grado_id = ? AND activa = 1");
        $stmtCheck->execute([$data['nombre'], $data['grado_id']]);
        if ($stmtCheck->fetch()) {
            $this->setFlash('error', 'Ya existe una materia con ese nombre para el grado seleccionado.');
            $this->redirect('materias/create');
            return;
        }

        if ($this->materiaModel->create($data)) {
            // Guardar evaluaciones
            $materiaId = (int)$db->lastInsertId();
            if (!empty($data['evaluaciones'])) {
                $this->materiaModel->setEvaluaciones($materiaId, $data['evaluaciones']);
            }

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
        $evaluaciones = $this->materiaModel->getEvaluaciones((int)$id);
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
        $id = (int)$id;

        if ($this->materiaModel->update($id, $data)) {
            // Guardar evaluaciones
            if (isset($data['evaluaciones'])) {
                $this->materiaModel->setEvaluaciones($id, $data['evaluaciones']);
            }

            $this->setFlash('success', 'Materia actualizada correctamente.');
            $this->redirect('materias');
        } else {
            $this->setFlash('error', 'Error al actualizar.');
            $this->redirect("materias/edit/$id");
        }
    }

    public function delete($id): void {
        $this->requireAuth('administrador');
        $this->validateCsrf();

        if ($this->materiaModel->delete((int)$id)) {
            $this->setFlash('success', 'Materia eliminada correctamente.');
        } else {
            $this->setFlash('error', 'No se pudo eliminar la materia.');
        }
        $this->redirect('materias');
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

        $profesorId = (int)$data['profesor_id'];
        $anio = $data['anio_lectivo'];
        $seccion = $data['seccion'];

        // Verificar si el profesor ya tiene esa materia en esa sección y año
        $db = Database::getInstance()->getConnection();
        $stmtCheck = $db->prepare("SELECT id FROM profesor_materia WHERE profesor_id = ? AND materia_id = ? AND anio_lectivo = ? AND seccion = ? AND activo = 1");
        $stmtCheck->execute([$profesorId, $id, $anio, $seccion]);
        if ($stmtCheck->fetch()) {
            $this->setFlash('error', 'Este profesor ya tiene asignada esta materia en la sección seleccionada.');
            $this->redirect("materias/assign/$id");
            return;
        }

        if ($this->materiaModel->assignProfessor((int)$id, $profesorId, $anio, $seccion)) {
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

    /**
     * API Endpoint para generar código sugerido (AJAX)
     */
    public function apiSugerirCodigo(): void {
        $this->requireAuth();
        $nombre = $_GET['nombre'] ?? '';
        if (empty($nombre)) {
            $this->json(['codigo' => '']);
            return;
        }
        $codigo = $this->materiaModel->generateSuggestedCode($nombre);
        $this->json(['codigo' => $codigo]);
    }
}
