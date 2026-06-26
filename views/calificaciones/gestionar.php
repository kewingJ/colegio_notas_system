<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-black text-gray-900"><?= h($materia['nombre']) ?></h3>
            <p class="text-xs text-gray-500 font-medium">Sección: <?= h($pm['seccion']) ?> | Gestión de Notas por Periodo</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= APP_URL ?>/calificacion" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-bold hover:bg-gray-200 transition-all">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>
    </div>

    <form action="<?= APP_URL ?>/calificacion/guardar" method="POST" class="p-6">
        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= Session::get(CSRF_TOKEN_NAME) ?>">
        <input type="hidden" name="pm_id" value="<?= $pm['id'] ?>">

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-widest text-gray-400 font-bold">
                        <th class="px-6 py-4">Carnet / Estudiante</th>
                        <?php foreach ($periodos as $p): ?>
                            <th class="px-6 py-4 text-center"><?= h($p['nombre']) ?></th>
                        <?php endforeach; ?>
                        <th class="px-6 py-4 text-center">Promedio</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($inscritos as $alumno): ?>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="text-xs font-mono font-bold text-blue-600 mb-0.5"><?= h($alumno['alumno_carnet']) ?></p>
                                <p class="text-sm font-bold text-gray-900"><?= h($alumno['nombre']) ?></p>
                            </td>
                            <?php
                            $suma = 0;
                            $count = 0;
                            foreach ($periodos as $p):
                                $nota = $alumno['notas'][$p['id']] ?? '';
                                if ($nota !== '') {
                                    $suma += $nota;
                                    $count++;
                                }
                            ?>
                                <td class="px-4 py-4 text-center">
                                    <input type="number" step="0.1" min="0" max="100"
                                           name="notas[<?= $alumno['inscripcion_id'] ?>][<?= $p['id'] ?>]"
                                           value="<?= $nota ?>"
                                           class="w-16 px-2 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-center font-bold focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                </td>
                            <?php endforeach; ?>
                            <td class="px-6 py-4 text-center">
                                <?php $promedio = $count > 0 ? $suma / $count : 0; ?>
                                <span class="text-sm font-black <?= $promedio >= 70 ? 'text-green-600' : 'text-red-600' ?>">
                                    <?= number_format($promedio, 1) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (empty($inscritos)): ?>
            <div class="py-12 text-center">
                <p class="text-gray-500 text-sm">No hay alumnos inscritos en esta materia aún.</p>
                <a href="<?= APP_URL ?>/materias" class="text-blue-600 text-xs font-bold mt-2 inline-block hover:underline">Ir a Gestión de Materias para inscribir</a>
            </div>
        <?php else: ?>
            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl text-sm font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all flex items-center gap-2">
                    <i class="fas fa-save"></i> Guardar Calificaciones
                </button>
            </div>
        <?php endif; ?>
    </form>
</div>
