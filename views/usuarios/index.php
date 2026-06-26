<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

    <!-- Filtros y Búsqueda -->
    <div class="p-6 border-b border-gray-100 flex flex-wrap gap-4 items-center justify-between">
        <form action="<?= APP_URL ?>/usuarios" method="GET" class="flex flex-wrap gap-3 items-center">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input type="text" name="search" value="<?= h($search) ?>" placeholder="Nombre o email..."
                    class="pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none">
            </div>

            <select name="rol_id" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white outline-none">
                <option value="">Todos los Roles</option>
                <?php foreach ($roles as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= $rol_id == $r['id'] ? 'selected' : '' ?>><?= ucfirst($r['nombre']) ?></option>
                <?php endforeach; ?>
            </select>

            <select name="activo" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white outline-none">
                <option value="">Todos los Estados</option>
                <option value="1" <?= $activo === '1' ? 'selected' : '' ?>>Activos</option>
                <option value="0" <?= $activo === '0' ? 'selected' : '' ?>>Inactivos</option>
            </select>

            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-gray-900 transition-all">
                Filtrar
            </button>
            <?php if (!empty($search) || !empty($rol_id) || $activo !== ''): ?>
                <a href="<?= APP_URL ?>/usuarios" class="text-xs text-gray-500 font-bold hover:underline">Limpiar</a>
            <?php endif; ?>
        </form>

        <a href="<?= APP_URL ?>/usuarios/create" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all flex items-center gap-2">
            <i class="fas fa-plus"></i> Nuevo Usuario
        </a>
    </div>

    <!-- Tabla -->
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 text-[10px] uppercase tracking-widest text-gray-400 font-bold">
                    <th class="px-6 py-4">Usuario</th>
                    <th class="px-6 py-4">Rol</th>
                    <th class="px-6 py-4">Estado</th>
                    <th class="px-6 py-4">Último Acceso</th>
                    <th class="px-6 py-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">No se encontraron usuarios.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($users as $u): ?>
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                                <?= substr($u['nombre'], 0, 1) ?>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900"><?= h($u['nombre']) ?></p>
                                <p class="text-xs text-gray-500"><?= h($u['email']) ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase <?= $u['rol_nombre'] === 'administrador' ? 'bg-blue-100 text-blue-600' : 'bg-purple-100 text-purple-600' ?>">
                            <?= $u['rol_nombre'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-1.5">
                            <span class="h-2 w-2 rounded-full <?= $u['activo'] ? 'bg-green-500' : 'bg-red-500' ?>"></span>
                            <span class="text-xs font-medium text-gray-600"><?= $u['activo'] ? 'Activo' : 'Inactivo' ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs text-gray-500"><?= $u['ultimo_login'] ? date('d/m/Y H:i', strtotime($u['ultimo_login'])) : 'Nunca' ?></span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="<?= APP_URL ?>/usuarios/edit/<?= $u['id'] ?>" class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?= APP_URL ?>/usuarios/toggleStatus/<?= $u['id'] ?>" method="POST" class="inline">
                                <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= Session::get(CSRF_TOKEN_NAME) ?>">
                                <button type="submit" class="p-2 text-gray-400 hover:text-<?= $u['activo'] ? 'red' : 'green' ?>-600 transition-colors"
                                    title="<?= $u['activo'] ? 'Desactivar' : 'Activar' ?>"
                                    onclick="return confirm('¿Seguro que desea cambiar el estado de este usuario?')">
                                    <i class="fas fa-power-off"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <?php require_once __DIR__ . '/../partials/pagination.php'; ?>

</div>
