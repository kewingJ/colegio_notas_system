<?php
class Estudiante extends Model {
    private ApiRestService $apiService;

    public function __construct() {
        parent::__construct();
        $this->apiService = new ApiRestService($this->db);
    }

    public function getAll(int $page = 1, int $perPage = 15, array $filters = []): array {
        $alumnos = $this->apiService->getAlumnosActivos();

        // Aplicar filtros
        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $alumnos = array_filter($alumnos, function($a) use ($search) {
                return strpos(strtolower($a['nombre']), $search) !== false ||
                       strpos(strtolower($a['carnet']), $search) !== false;
            });
        }

        if (!empty($filters['nivel'])) {
            $alumnos = array_filter($alumnos, function($a) use ($filters) {
                return $a['academico']['nivel'] === $filters['nivel'];
            });
        }

        // Paginación manual ya que viene de API
        $total = count($alumnos);
        $offset = ($page - 1) * $perPage;
        $sliced = array_slice($alumnos, $offset, $perPage);

        return [
            'data' => $sliced,
            'total' => $total
        ];
    }

    public function findByCarnet(string $carnet): ?array {
        $alumnos = $this->apiService->getAlumnosActivos();
        foreach ($alumnos as $alumno) {
            if ($alumno['carnet'] === $carnet) {
                return $alumno;
            }
        }
        return null;
    }

    public function getInscripciones(string $carnet): array {
        $stmt = $this->db->prepare("
            SELECT i.*, m.nombre as materia_nombre, m.codigo as materia_codigo,
                   u.nombre as profesor_nombre, pm.seccion
            FROM inscripciones i
            JOIN profesor_materia pm ON i.profesor_materia_id = pm.id
            JOIN materias m ON pm.materia_id = m.id
            JOIN usuarios u ON pm.profesor_id = u.id
            WHERE i.alumno_carnet = ?
            ORDER BY i.anio_lectivo DESC
        ");
        $stmt->execute([$carnet]);
        return $stmt->fetchAll();
    }
}
