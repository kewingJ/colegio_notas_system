<!-- KPI Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <!-- Alumnos -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5">
        <div class="bg-blue-100 text-blue-600 w-14 h-14 rounded-2xl flex items-center justify-center text-2xl">
            <i class="fas fa-user-graduate"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Total Alumnos</p>
            <h3 class="text-2xl font-bold text-gray-900"><?= $totalAlumnos ?></h3>
            <p class="text-[10px] text-green-500 font-bold mt-1"><i class="fas fa-sync"></i> Sincronizado</p>
        </div>
    </div>

    <!-- Profesores -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5">
        <div class="bg-purple-100 text-purple-600 w-14 h-14 rounded-2xl flex items-center justify-center text-2xl">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Profesores Activos</p>
            <h3 class="text-2xl font-bold text-gray-900"><?= $totalProfesores ?></h3>
            <p class="text-[10px] text-gray-400 font-bold mt-1">En base de datos</p>
        </div>
    </div>

    <!-- Materias -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5">
        <div class="bg-orange-100 text-orange-600 w-14 h-14 rounded-2xl flex items-center justify-center text-2xl">
            <i class="fas fa-book"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Materias Totales</p>
            <h3 class="text-2xl font-bold text-gray-900"><?= $totalMaterias ?></h3>
            <p class="text-[10px] text-gray-400 font-bold mt-1">Plan de estudios</p>
        </div>
    </div>

    <!-- Horarios -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5">
        <div class="bg-green-100 text-green-600 w-14 h-14 rounded-2xl flex items-center justify-center text-2xl">
            <i class="fas fa-clock"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Horarios Hoy</p>
            <h3 class="text-2xl font-bold text-gray-900"><?= $totalHorarios ?></h3>
            <p class="text-[10px] text-gray-400 font-bold mt-1">Para el ciclo <?= $anioActivo ?></p>
        </div>
    </div>

</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Main Panel: Configuration & Sync -->
    <div class="lg:col-span-2 space-y-8">

        <!-- Year Selection & API Status -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">Configuración del Sistema</h3>
                <div class="flex items-center gap-2">
                    <span class="flex h-3 w-3 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full <?= $apiOnline ? 'bg-green-400' : 'bg-red-400' ?> opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 <?= $apiOnline ? 'bg-green-500' : 'bg-red-500' ?>"></span>
                    </span>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                        API: <?= $apiOnline ? 'Online' : 'Offline' ?>
                    </span>
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Change Year Form -->
                <form action="<?= APP_URL ?>/dashboard/updateAnioLectivo" method="POST" class="space-y-4">
                    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= Session::get(CSRF_TOKEN_NAME) ?>">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Año Lectivo Activo</label>
                        <div class="flex gap-2">
                            <input type="number" name="anio_lectivo" value="<?= $anioActivo ?>"
                                class="flex-1 px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">
                                Actualizar
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Sync API -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Sincronización de Datos</label>
                    <div class="bg-gray-50 p-4 rounded-xl flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500">Última vez:</p>
                            <p class="text-sm font-bold text-gray-900"><?= $fechaSync ?: 'Nunca' ?></p>
                        </div>
                        <a href="<?= APP_URL ?>/apiSync/run" class="flex items-center gap-2 text-blue-600 font-bold text-sm hover:underline">
                            <i class="fas fa-sync-alt"></i> Sincronizar Ahora
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Latest Users Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Usuarios Registrados Recientemente</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 text-[10px] uppercase tracking-widest text-gray-400 font-bold">
                            <th class="px-6 py-4">Usuario</th>
                            <th class="px-6 py-4">Rol</th>
                            <th class="px-6 py-4">Estado</th>
                            <th class="px-6 py-4 text-right">Creado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($usuariosRecientes as $u): ?>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center font-bold text-xs text-gray-500">
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
                            <td class="px-6 py-4 text-right">
                                <span class="text-xs text-gray-400"><?= date('d/m/Y', strtotime($u['created_at'])) ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-gray-50 border-t border-gray-100 text-center">
                <a href="<?= APP_URL ?>/usuarios" class="text-sm font-bold text-blue-600 hover:text-blue-700">Ver todos los usuarios <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
        </div>

    </div>

    <!-- Side Panel: Stats & Info -->
    <div class="space-y-8">

        <!-- Academic Efficiency Card -->
        <div class="bg-blue-600 rounded-2xl shadow-xl shadow-blue-200 p-6 text-white relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-blue-100 text-sm font-medium">Eficiencia Académica</p>
                <div class="flex items-end gap-3 mt-2">
                    <h2 class="text-4xl font-black">--</h2>
                    <span class="text-blue-100 text-sm mb-1 font-bold">N/A</span>
                </div>
                <div class="mt-6">
                    <div class="h-2 w-full bg-blue-500 rounded-full overflow-hidden">
                        <div class="h-full bg-white rounded-full" style="width: 0%"></div>
                    </div>
                </div>
                <p class="mt-4 text-xs text-blue-100 flex items-center gap-2">
                    <i class="fas fa-info-circle"></i>
                    Disponible cuando se registren notas.
                </p>
            </div>
            <!-- Decorative circle -->
            <div class="absolute -bottom-12 -right-12 w-40 h-40 bg-blue-500 rounded-full opacity-20"></div>
        </div>

        <!-- System Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-900 mb-4">Información del Sistema</h3>
            <ul class="space-y-4">
                <li class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Versión</span>
                    <span class="font-bold text-gray-900"><?= APP_VERSION ?></span>
                </li>
                <li class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Entorno</span>
                    <span class="px-2 py-0.5 rounded bg-orange-100 text-orange-600 text-[10px] font-black uppercase"><?= APP_ENV ?></span>
                </li>
                <li class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">PHP Version</span>
                    <span class="font-bold text-gray-900"><?= PHP_VERSION ?></span>
                </li>
            </ul>
        </div>

    </div>

</div>
