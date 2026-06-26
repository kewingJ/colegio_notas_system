<!-- KPI Summary (Same style as image) -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 border-l-4 border-l-blue-500">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Materias</p>
        <h3 class="text-3xl font-black text-gray-900 mt-1"><?= $total ?></h3>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 border-l-4 border-l-purple-500">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Niveles</p>
        <h3 class="text-3xl font-black text-gray-900 mt-1"><?= count($niveles) ?></h3>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 border-l-4 border-l-green-500">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Eficiencia</p>
        <h3 class="text-3xl font-black text-gray-900 mt-1"><?= $eficiencia ?>%</h3>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 border-l-4 border-l-orange-500">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Cupos Libres</p>
        <h3 class="text-3xl font-black text-gray-900 mt-1"><?= $cuposLibres ?></h3>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

    <div class="p-6 border-b border-gray-100 flex flex-wrap gap-4 items-center justify-between">
        <form action="<?= APP_URL ?>/materias" method="GET" class="flex flex-wrap gap-3 items-center">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input type="text" name="search" value="<?= h($search) ?>" placeholder="Código o nombre..."
                    class="pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none">
            </div>

            <select name="nivel_id" onchange="this.form.submit()" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white outline-none">
                <option value="">Todos los Niveles</option>
                <?php foreach ($niveles as $n): ?>
                    <option value="<?= $n['id'] ?>" <?= $nivel_id == $n['id'] ? 'selected' : '' ?>><?= h($n['nombre']) ?></option>
                <?php endforeach; ?>
            </select>

            <?php if ($nivel_id): ?>
            <select name="grado_id" onchange="this.form.submit()" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white outline-none">
                <option value="">Todos los Grados</option>
                <?php foreach ($grados as $g): ?>
                    <option value="<?= $g['id'] ?>" <?= $grado_id == $g['id'] ? 'selected' : '' ?>><?= h($g['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <?php endif; ?>

            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-gray-900 transition-all">
                Filtrar
            </button>
        </form>

        <a href="<?= APP_URL ?>/materias/create" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all flex items-center gap-2">
            <i class="fas fa-plus"></i> Crear Nueva Materia
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 text-[10px] uppercase tracking-widest text-gray-400 font-bold">
                    <th class="px-6 py-4">Código</th>
                    <th class="px-6 py-4">Materia</th>
                    <th class="px-6 py-4">Nivel / Grado</th>
                    <th class="px-6 py-4">Profesor Responsable</th>
                    <th class="px-6 py-4">Inscritos</th>
                    <th class="px-6 py-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($materias as $m): ?>
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="font-mono text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded"><?= h($m['codigo']) ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-gray-900"><?= h($m['nombre']) ?></p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-xs text-gray-700 font-medium"><?= h($m['nivel_nombre']) ?></p>
                        <p class="text-[10px] text-gray-400 uppercase font-bold"><?= h($m['grado_nombre']) ?></p>
                    </td>
                    <td class="px-6 py-4">
                        <?php if ($m['profesor_nombre']): ?>
                            <div class="flex items-center gap-2">
                                <div class="h-6 w-6 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-[10px] font-bold">
                                    <?= substr($m['profesor_nombre'], 0, 1) ?>
                                </div>
                                <span class="text-xs font-medium text-gray-700"><?= h($m['profesor_nombre']) ?></span>
                                <span class="text-[10px] bg-gray-100 px-1 rounded text-gray-500 font-bold">Sec. <?= $m['seccion'] ?></span>
                            </div>
                        <?php else: ?>
                            <span class="text-xs text-gray-400 italic">No asignado</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex-1 h-1.5 w-16 bg-gray-100 rounded-full overflow-hidden">
                                <?php
                                $percent = $m['cupo_maximo'] > 0 ? ($m['inscritos'] / $m['cupo_maximo']) * 100 : 0;
                                $color = $percent > 90 ? 'bg-red-500' : ($percent > 50 ? 'bg-yellow-500' : 'bg-green-500');
                                ?>
                                <div class="h-full <?= $color ?>" style="width: <?= min(100, $percent) ?>%"></div>
                            </div>
                            <span class="text-xs font-bold text-gray-700">
                                <?= $m['inscritos'] ?><?= $m['cupo_maximo'] > 0 ? '/' . $m['cupo_maximo'] : '' ?>
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="<?= APP_URL ?>/materias/enroll/<?= $m['id'] ?>" class="p-2 text-gray-400 hover:text-green-600 transition-colors" title="Inscribir Alumnos">
                                <i class="fas fa-user-graduate"></i>
                            </a>
                            <a href="<?= APP_URL ?>/materias/assign/<?= $m['id'] ?>" class="p-2 text-gray-400 hover:text-purple-600 transition-colors" title="Asignar Profesor">
                                <i class="fas fa-user-plus"></i>
                            </a>
                            <a href="<?= APP_URL ?>/materias/edit/<?= $m['id'] ?>" class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?= APP_URL ?>/materias/delete/<?= $m['id'] ?>" method="POST" class="inline" onsubmit="return confirm('¿Está seguro de eliminar esta materia?')">
                                <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= Session::get(CSRF_TOKEN_NAME) ?>">
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php require_once __DIR__ . '/../partials/pagination.php'; ?>

</div>
