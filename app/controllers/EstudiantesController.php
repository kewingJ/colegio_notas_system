<?php
class EstudiantesController extends Controller {
    private Estudiante $estudianteModel;
    private Materia $materiaModel;

    public function __construct() {
        $this->estudianteModel = new Estudiante();
        $this->materiaModel = new Materia();
    }

    public function index(): void {
        $this->requireAuth();

        $page = (int)($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        $nivel = $_GET['nivel'] ?? '';

        $filters = ['search' => $search, 'nivel' => $nivel];
        $result = $this->estudianteModel->getAll($page, DEFAULT_PAGE_SIZE, $filters);

        $estudiantes = $result['data'];
        $total = $result['total'];
        $niveles = $this->materiaModel->getNiveles();

        ob_start();
        extract([
            'estudiantes' => $estudiantes,
            'niveles' => $niveles,
            'total' => $total,
            'page' => $page,
            'search' => $search,
            'nivel' => $nivel,
            'perPage' => DEFAULT_PAGE_SIZE
        ]);
        require_once __DIR__ . '/../../views/estudiantes/index.php';
        $content = ob_get_clean();

        $this->view('layouts/main', [
            'title' => 'Gestión de Estudiantes',
            'subtitle' => 'Listado oficial de alumnos matriculados desde el sistema central.',
            'content' => $content
        ]);
    }

    public function show($carnet): void {
        $this->requireAuth();
        $estudiante = $this->estudianteModel->findByCarnet($carnet);

        if (!$estudiante) {
            $this->setFlash('error', 'Estudiante no encontrado.');
            $this->redirect('estudiantes');
        }

        $inscripciones = $this->estudianteModel->getInscripciones($carnet);
        $calificacionModel = new Calificacion();

        foreach ($inscripciones as &$ins) {
            $ins['notas'] = $calificacionModel->getByInscripcion($ins['id']);
        }

        ob_start();
        require_once __DIR__ . '/../../views/estudiantes/show.php';
        $content = ob_get_clean();

        $this->view('layouts/main', [
            'title' => 'Perfil del Estudiante',
            'content' => $content
        ]);
    }
}
