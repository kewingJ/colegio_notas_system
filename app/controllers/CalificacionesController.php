<?php
class CalificacionesController extends Controller {
    private Materia $materiaModel;
    private Periodo $periodoModel;
    private Calificacion $calificacionModel;
    private Inscripcion $inscripcionModel;

    public function __construct() {
        $this->materiaModel = new Materia();
        $this->periodoModel = new Periodo();
        $this->calificacionModel = new Calificacion();
        $this->inscripcionModel = new Inscripcion();
    }

    public function index(): void {
        $this->requireAuth();

        $role = Session::get('role_name');
        $userId = Session::get('user_id');

        // Obtener materias asignadas
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT pm.id, m.nombre as materia_nombre, m.codigo, pm.seccion, n.nombre as nivel_nombre, g.nombre as grado_nombre, u.nombre as profesor_nombre
                FROM profesor_materia pm
                JOIN materias m ON pm.materia_id = m.id
                JOIN niveles n ON m.nivel_id = n.id
                JOIN grados g ON m.grado_id = g.id
                JOIN usuarios u ON pm.profesor_id = u.id
                WHERE pm.activo = 1";

        if ($role === 'profesor') {
            $sql .= " AND pm.profesor_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$userId]);
        } else {
            $stmt = $db->prepare($sql);
            $stmt->execute();
        }

        $asignaciones = $stmt->fetchAll();

        ob_start();
        extract(['asignaciones' => $asignaciones]);
        require_once __DIR__ . '/../../views/calificaciones/index.php';
        $content = ob_get_clean();

        $this->view('layouts/main', [
            'title' => 'Control de Calificaciones',
            'subtitle' => 'Seleccione una materia para gestionar las notas por periodo.',
            'content' => $content
        ]);
    }

    public function gestionar($profesorMateriaId): void {
        $this->requireAuth();

        // Verificar que el profesor tenga acceso a esta materia (o sea admin)
        $db = Database::getInstance()->getConnection();
        $stmtVerif = $db->prepare("SELECT * FROM profesor_materia WHERE id = ?");
        $stmtVerif->execute([$profesorMateriaId]);
        $pm = $stmtVerif->fetch();

        if (!$pm || (Session::get('role_name') === 'profesor' && $pm['profesor_id'] != Session::get('user_id'))) {
            $this->setFlash('error', 'No tienes permiso para acceder a esta materia.');
            $this->redirect('calificaciones');
        }

        $materia = $this->materiaModel->findById($pm['materia_id']);
        $periodos = $this->periodoModel->getAllActive();

        // Obtener alumnos inscritos y sus notas
        $stmtAlumnos = $db->prepare("
            SELECT i.id as inscripcion_id, i.alumno_carnet
            FROM inscripciones i
            WHERE i.profesor_materia_id = ?
            ORDER BY i.alumno_carnet
        ");
        $stmtAlumnos->execute([$profesorMateriaId]);
        $inscritos = $stmtAlumnos->fetchAll();

        $estudianteModel = new Estudiante();
        foreach ($inscritos as &$ins) {
            $apiData = $estudianteModel->findByCarnet($ins['alumno_carnet']);
            $ins['nombre'] = $apiData ? $apiData['nombre'] : 'Desconocido';

            // Cargar notas actuales
            $notas = $this->calificacionModel->getByInscripcion($ins['inscripcion_id']);
            $ins['notas'] = [];
            foreach ($notas as $n) {
                $ins['notas'][$n['periodo_id']] = $n['nota'];
            }
        }

        ob_start();
        extract([
            'materia' => $materia,
            'pm' => $pm,
            'periodos' => $periodos,
            'inscritos' => $inscritos
        ]);
        require_once __DIR__ . '/../../views/calificaciones/gestionar.php';
        $content = ob_get_clean();

        $this->view('layouts/main', [
            'title' => 'Gestión de Notas',
            'content' => $content
        ]);
    }

    public function guardar(): void {
        $this->requireAuth();
        $this->validateCsrf();

        $data = $this->getPost();
        $notas = $data['notas'] ?? []; // [inscripcion_id][periodo_id] = nota

        $success = true;
        foreach ($notas as $inscripcionId => $periodoNotas) {
            foreach ($periodoNotas as $periodoId => $nota) {
                if ($nota === '') continue;
                if (!$this->calificacionModel->save((int)$inscripcionId, (int)$periodoId, (float)$nota)) {
                    $success = false;
                }
            }
        }

        if ($success) {
            $this->setFlash('success', 'Calificaciones actualizadas correctamente.');
        } else {
            $this->setFlash('error', 'Hubo un error al guardar algunas notas.');
        }

        $this->redirect('calificacion/gestionar/' . $data['pm_id']);
    }
}
