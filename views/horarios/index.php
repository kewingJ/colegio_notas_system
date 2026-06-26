<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
    <form action="<?= APP_URL ?>/horarios" method="GET" class="flex items-end gap-4">
        <div class="flex-1 max-w-sm">
            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Seleccionar Profesor</label>
            <select name="profesor_id" onchange="this.form.submit()"
                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                <option value="">-- Seleccione un docente --</option>
                <?php foreach ($profesores as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $profesor_id == $p['id'] ? 'selected' : '' ?>><?= h($p['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if ($profesor_id): ?>
            <a href="<?= APP_URL ?>/horarios/asignar?profesor_id=<?= $profesor_id ?>" class="bg-blue-600 text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">
                <i class="fas fa-plus mr-1"></i> Agregar Bloque
            </a>
        <?php endif; ?>
    </form>
</div>

<?php if ($profesor_id): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-[10px] uppercase tracking-widest text-gray-400 font-bold">
                        <th class="px-4 py-4 border-r border-gray-100 w-24">Hora</th>
                        <?php
                        $dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado'];
                        foreach ($dias as $d): ?>
                            <th class="px-4 py-4 border-r border-gray-100"><?= $d ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php
                    $bloques = [
                        '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'
                    ];
                    foreach ($bloques as $hora): ?>
                        <tr>
                            <td class="px-4 py-6 bg-gray-50/30 text-center border-r border-gray-100 text-xs font-bold text-gray-500 italic"><?= $hora ?></td>
                            <?php foreach ($dias as $numDia => $nombreDia): ?>
                                <td class="px-2 py-2 border-r border-gray-100 relative min-h-[80px]">
                                    <?php
                                    $clase = array_filter($horarios, function($h) use ($numDia, $hora) {
                                        return $h['dia_semana'] == $numDia && substr($h['hora_inicio'], 0, 5) <= $hora && substr($h['hora_fin'], 0, 5) > $hora;
                                    });
                                    if (!empty($clase)):
                                        $c = reset($clase); ?>
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 group">
                                            <p class="text-[10px] font-black text-blue-600 uppercase tracking-tighter mb-1"><?= h($c['materia_codigo']) ?></p>
                                            <p class="text-xs font-bold text-gray-900 leading-tight"><?= h($c['materia_nombre']) ?></p>
                                            <p class="text-[10px] text-gray-500 mt-1"><i class="fas fa-door-open mr-1"></i> Aula: <?= h($c['aula'] ?: 'N/A') ?></p>
                                            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <form action="<?= APP_URL ?>/horarios/delete/<?= $c['id'] ?>" method="POST" class="inline">
                                                    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= Session::get(CSRF_TOKEN_NAME) ?>">
                                                    <input type="hidden" name="profesor_id" value="<?= $profesor_id ?>">
                                                    <button type="submit"
                                                       onclick="return confirm('¿Eliminar este bloque?')"
                                                       class="text-red-400 hover:text-red-600">
                                                        <i class="fas fa-times-circle"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <a href="<?= APP_URL ?>/horarios/asignar?profesor_id=<?= $profesor_id ?>&dia=<?= $numDia ?>&hora=<?= $hora ?>"
                                           class="w-full h-full min-h-[60px] flex items-center justify-center text-gray-200 hover:text-blue-400 hover:bg-blue-50/30 rounded-lg transition-all group">
                                            <i class="fas fa-plus-circle opacity-0 group-hover:opacity-100"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Materias Asignadas List -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($materiasAsignadas as $ma): ?>
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="bg-purple-100 text-purple-600 w-10 h-10 rounded-lg flex items-center justify-center font-bold text-xs">
                    <?= substr($ma['nombre'], 0, 1) ?>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900"><?= h($ma['nombre']) ?></p>
                    <p class="text-[10px] text-gray-500 font-bold uppercase"><?= h($ma['codigo']) ?> | Sección <?= $ma['seccion'] ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="bg-white rounded-2xl border border-gray-100 border-dashed p-20 text-center">
        <div class="bg-gray-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300 text-3xl">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900">Seleccione un profesor</h3>
        <p class="text-gray-500 max-w-xs mx-auto mt-2">Debe elegir un docente de la lista superior para visualizar o gestionar su horario semanal.</p>
    </div>
<?php endif; ?>
