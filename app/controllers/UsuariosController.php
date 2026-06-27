<?php
class UsuariosController extends Controller {
    private Usuario $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    public function index(): void {
        $this->requireAuth('administrador');

        $page = (int)($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        $rol_id = $_GET['rol_id'] ?? '';
        $activo = $_GET['activo'] ?? '';

        $filters = ['search' => $search, 'rol_id' => $rol_id, 'activo' => $activo];
        $users = $this->usuarioModel->getAll($page, DEFAULT_PAGE_SIZE, $filters);
        $total = $this->usuarioModel->countAll($filters);
        $roles = $this->usuarioModel->getRoles();

        ob_start();
        extract([
            'users' => $users,
            'roles' => $roles,
            'total' => $total,
            'page' => $page,
            'search' => $search,
            'rol_id' => $rol_id,
            'activo' => $activo,
            'perPage' => DEFAULT_PAGE_SIZE
        ]);
        require_once __DIR__ . '/../../views/usuarios/index.php';
        $content = ob_get_clean();

        $this->view('layouts/main', [
            'title' => 'Gestión de Usuarios',
            'subtitle' => 'Administra los accesos al sistema',
            'content' => $content
        ]);
    }

    public function create(): void {
        $this->requireAuth('administrador');
        $roles = $this->usuarioModel->getRoles();

        // Para el Wizard de Profesor
        $materiaModel = new Materia();
        $materias = $materiaModel->getAll(1, 1000); // Obtener todas para el select
        $niveles = $materiaModel->getNiveles();

        ob_start();
        extract([
            'roles' => $roles,
            'materias' => $materias,
            'niveles' => $niveles
        ]);
        require_once __DIR__ . '/../../views/usuarios/create.php';
        $content = ob_get_clean();

        $this->view('layouts/main', [
            'title' => 'Nuevo Usuario',
            'content' => $content
        ]);
    }

    public function store(): void {
        $this->requireAuth('administrador');
        $this->validateCsrf();

        $data = $this->getPost();

        // Validación básica
        if (empty($data['nombre']) || empty($data['email']) || empty($data['password'])) {
            $this->setFlash('error', 'Todos los campos son obligatorios.');
            $this->redirect('usuarios/create');
        }

        if ($data['password'] !== $data['confirm_password']) {
            $this->setFlash('error', 'Las contraseñas no coinciden.');
            $this->redirect('usuarios/create');
        }

        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        try {
            if ($this->usuarioModel->create($data)) {
                $userId = (int)$db->lastInsertId();

                // Manejo de Wizard de Profesor (Asignación de materia)
                if (!empty($data['materia_id']) || !empty($data['new_materia_nombre'])) {
                    $materiaId = !empty($data['materia_id']) ? (int)$data['materia_id'] : null;

                    // Crear nueva materia si se solicitó
                    if (!$materiaId && !empty($data['new_materia_nombre'])) {
                        $materiaModel = new Materia();
                        $newMateriaData = [
                            'nombre' => $data['new_materia_nombre'],
                            'codigo' => $data['new_materia_codigo'],
                            'nivel_id' => $data['new_materia_nivel'],
                            'grado_id' => $data['new_materia_grado'],
                            'cupo_maximo' => 0,
                            'activa' => 1
                        ];
                        if ($materiaModel->create($newMateriaData)) {
                            $materiaId = (int)$db->lastInsertId();
                        }
                    }

                    if ($materiaId) {
                        $materiaModel = new Materia();
                        $stmtConfig = $db->prepare("SELECT valor FROM configuracion WHERE clave = 'anio_lectivo_activo'");
                        $stmtConfig->execute();
                        $anioActivo = $stmtConfig->fetchColumn() ?: date('Y');
                        $seccion = $data['seccion'] ?? 'A';

                        $materiaModel->assignProfessor($materiaId, $userId, $anioActivo, $seccion);
                    }
                }

                $db->commit();
                $this->setFlash('success', 'Usuario creado correctamente.');
                $this->redirect('usuarios');
            }
        } catch (Exception $e) {
            $db->rollBack();
            $this->setFlash('error', 'Error al crear usuario: ' . $e->getMessage());
            $this->redirect('usuarios/create');
        }
    }

    public function edit($id): void {
        $this->requireAuth('administrador');
        $user = $this->usuarioModel->findById((int)$id);
        if (!$user) {
            $this->setFlash('error', 'Usuario no encontrado.');
            $this->redirect('usuarios');
        }
        $roles = $this->usuarioModel->getRoles();

        ob_start();
        require_once __DIR__ . '/../../views/usuarios/edit.php';
        $content = ob_get_clean();

        $this->view('layouts/main', [
            'title' => 'Editar Usuario',
            'content' => $content
        ]);
    }

    public function update($id): void {
        $this->requireAuth('administrador');
        $this->validateCsrf();

        $data = $this->getPost();
        $id = (int)$id;
        $currentUser = $this->usuarioModel->findById($id);

        // Protección de integridad: No cambiar rol si tiene datos asociados
        if ($currentUser['rol_id'] != $data['rol_id']) {
            $db = Database::getInstance()->getConnection();

            // Si es profesor, verificar si tiene materias o horarios asignados
            if ($currentUser['rol_nombre'] === 'profesor') {
                $hasMaterias = $db->prepare("SELECT COUNT(*) FROM profesor_materia WHERE profesor_id = ?");
                $hasMaterias->execute([$id]);
                $hasHorarios = $db->prepare("SELECT COUNT(*) FROM horarios WHERE profesor_id = ?");
                $hasHorarios->execute([$id]);

                if ($hasMaterias->fetchColumn() > 0 || $hasHorarios->fetchColumn() > 0) {
                    $this->setFlash('error', 'No se puede cambiar el rol de un profesor con materias o horarios asignados.');
                    $this->redirect("usuarios/edit/$id");
                }
            }
        }

        if (!empty($data['password']) && $data['password'] !== $data['confirm_password']) {
            $this->setFlash('error', 'Las contraseñas no coinciden.');
            $this->redirect("usuarios/edit/$id");
        }

        try {
            if ($this->usuarioModel->update($id, $data)) {
                $this->setFlash('success', 'Usuario actualizado correctamente.');
                $this->redirect('usuarios');
            }
        } catch (Exception $e) {
            $this->setFlash('error', 'Error al actualizar: ' . $e->getMessage());
            $this->redirect("usuarios/edit/$id");
        }
    }

    public function toggleStatus($id): void {
        $this->requireAuth('administrador');
        $this->validateCsrf();

        $id = (int)$id;
        $user = $this->usuarioModel->findById($id);

        if (!$user) {
            $this->setFlash('error', 'Usuario no encontrado.');
            $this->redirect('usuarios');
        }

        $newStatus = $user['activo'] ? 0 : 1;
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE usuarios SET activo = ? WHERE id = ?");
        $stmt->execute([$newStatus, $id]);

        $this->setFlash('success', 'Estado de usuario actualizado.');
        $this->redirect('usuarios');
    }
}
