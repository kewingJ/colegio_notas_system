<?php
class HorariosController extends Controller {
    private Horario $horarioModel;
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->horarioModel = new Horario();
    }

    public function index(): void {
        $this->requireAuth();

        $profesor_id = $_GET['profesor_id'] ?? null;

        // Obtener lista de profesores para el selector
        $stmtProf = $this->db->prepare("SELECT id, nombre FROM usuarios WHERE rol_id = (SELECT id FROM roles WHERE nombre = 'profesor') AND activo = 1 ORDER BY nombre");
        $stmtProf->execute();
        $profesores = $stmtProf->fetchAll();

        $anioActivo = $this->db->query("SELECT valor FROM configuracion WHERE clave = 'anio_lectivo_activo'")->fetchColumn() ?: date('Y');

        $horarios = [];
        $materiasAsignadas = [];
        if ($profesor_id) {
            $horarios = $this->horarioModel->getByProfesor((int)$profesor_id, $anioActivo);
            $materiasAsignadas = $this->horarioModel->getMateriasAsignadas((int)$profesor_id, $anioActivo);
        }

        ob_start();
        extract([
            'profesores' => $profesores,
            'profesor_id' => $profesor_id,
            'horarios' => $horarios,
            'materiasAsignadas' => $materiasAsignadas,
            'anioActivo' => $anioActivo
        ]);
        require_once __DIR__ . '/../../views/horarios/index.php';
        $content = ob_get_clean();

        $this->view('layouts/main', [
            'title' => 'Gestión de Horarios',
            'subtitle' => 'Organiza la carga horaria semanal de los docentes.',
            'content' => $content
        ]);
    }

    public function asignar(): void {
        $this->requireAuth('administrador');
        $profesor_id = $_GET['profesor_id'] ?? null;
        if (!$profesor_id) $this->redirect('horarios');

        $anioActivo = $this->db->query("SELECT valor FROM configuracion WHERE clave = 'anio_lectivo_activo'")->fetchColumn() ?: date('Y');
        $materias = $this->horarioModel->getMateriasAsignadas((int)$profesor_id, $anioActivo);

        $profesor = $this->db->prepare("SELECT nombre FROM usuarios WHERE id = ?");
        $profesor->execute([$profesor_id]);
        $profesorNombre = $profesor->fetchColumn();

        ob_start();
        extract([
            'profesor_id' => $profesor_id,
            'profesorNombre' => $profesorNombre,
            'materias' => $materias,
            'anioActivo' => $anioActivo,
            'dia' => $_GET['dia'] ?? 1,
            'hora' => $_GET['hora'] ?? ''
        ]);
        require_once __DIR__ . '/../../views/horarios/asignar.php';
        $content = ob_get_clean();

        $this->view('layouts/main', [
            'title' => 'Asignar Bloque de Horario',
            'content' => $content
        ]);
    }

    public function store(): void {
        $this->requireAuth('administrador');
        $this->validateCsrf();
        $data = $this->getPost();

        // Validar solapamiento
        if ($this->horarioModel->checkOverlap($data['profesor_id'], $data['dia_semana'], $data['hora_inicio'], $data['hora_fin'], $data['anio_lectivo'])) {
            $this->setFlash('error', 'Existe un solapamiento de horario para este profesor.');
            $this->redirect("horarios/asignar?profesor_id={$data['profesor_id']}");
        }

        if ($this->horarioModel->create($data)) {
            $this->setFlash('success', 'Bloque de horario asignado.');
            $this->redirect("horarios?profesor_id={$data['profesor_id']}");
        } else {
            $this->setFlash('error', 'Error al guardar el horario.');
            $this->redirect("horarios/asignar?profesor_id={$data['profesor_id']}");
        }
    }

    public function delete($id): void {
        $this->requireAuth('administrador');
        $this->validateCsrf();

        $profesor_id = $_POST['profesor_id'] ?? null;

        if ($this->horarioModel->delete((int)$id)) {
            $this->setFlash('success', 'Bloque eliminado.');
        } else {
            $this->setFlash('error', 'No se pudo eliminar.');
        }
        $this->redirect("horarios?profesor_id=$profesor_id");
    }
}
