<?php
class ApiSyncController extends Controller {
    private ApiRestService $apiService;
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->apiService = new ApiRestService($this->db);
    }

    public function run(): void {
        $this->requireAuth('administrador');

        if ($this->apiService->syncNivelesGrados()) {
            $stmt = $this->db->prepare("INSERT INTO configuracion (clave, valor) VALUES ('ultima_sincronizacion_api', ?) ON DUPLICATE KEY UPDATE valor = VALUES(valor)");
            $stmt->execute([date('d/m/Y H:i:s')]);

            $this->setFlash('success', 'Sincronización con la API completada exitosamente.');
        } else {
            $this->setFlash('error', 'No se pudo sincronizar con la API. Verifique la conexión.');
        }

        $this->redirect('dashboard');
    }
}
