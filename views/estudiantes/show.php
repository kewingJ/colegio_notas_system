<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Student Profile Card -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="h-24 bg-gradient-to-r from-blue-600 to-indigo-700"></div>
            <div class="px-6 pb-6">
                <div class="-mt-12 mb-4">
                    <div class="w-24 h-24 rounded-2xl bg-white p-1 shadow-lg">
                        <div class="w-full h-full rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-3xl font-black">
                            <?= substr($estudiante['nombre'], 0, 1) ?>
                        </div>
                    </div>
                </div>
                <h3 class="text-xl font-black text-gray-900"><?= h($estudiante['nombre']) ?></h3>
                <p class="text-sm font-bold text-blue-600 mb-4"><?= h($estudiante['carnet']) ?></p>

                <div class="space-y-4 pt-4 border-t border-gray-50">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Información Académica</p>
                        <div class="mt-2 space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Nivel:</span>
                                <span class="text-xs font-bold text-gray-900"><?= h($estudiante['academico']['nivel']) ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Grado:</span>
                                <span class="text-xs font-bold text-gray-900"><?= h($estudiante['academico']['grado']) ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Sección:</span>
                                <span class="text-xs font-bold text-gray-900"><?= h($estudiante['academico']['seccion']) ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Año:</span>
                                <span class="text-xs font-bold text-gray-900"><?= h($estudiante['academico']['anio_lectivo']) ?></span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Contacto</p>
                        <div class="mt-2 space-y-2">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-phone text-blue-500 text-xs"></i>
                                <span class="text-xs text-gray-700"><?= h($estudiante['contacto']['telefono']) ?></span>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-envelope text-blue-500 text-xs"></i>
                                <span class="text-xs text-gray-700 break-all"><?= h($estudiante['contacto']['email']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Parents Info -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <h4 class="text-sm font-black text-gray-900 mb-4">Padres / Tutores</h4>
            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400">
                        <i class="fas fa-user-tie text-xs"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Padre / Tutor</p>
                        <p class="text-xs font-bold text-gray-700"><?= h($estudiante['padre']) ?></p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400">
                        <i class="fas fa-user-nurse text-xs"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Madre / Tutor</p>
                        <p class="text-xs font-bold text-gray-700"><?= h($estudiante['madre']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Academic Record -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-black text-gray-900">Materias Inscritas e Historial</h3>
                <span class="bg-blue-600 text-white text-[10px] font-bold px-3 py-1 rounded-full">Año <?= h($estudiante['academico']['anio_lectivo']) ?></span>
            </div>

            <?php if (empty($inscripciones)): ?>
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-book-open text-gray-300 text-xl"></i>
                    </div>
                    <p class="text-gray-500 text-sm">No hay inscripciones registradas en este sistema para este alumno.</p>
                    <p class="text-xs text-gray-400 mt-1">Las materias deben ser asignadas por un administrador.</p>
                </div>
            <?php else: ?>
                <div class="divide-y divide-gray-50">
                    <?php foreach ($inscripciones as $ins): ?>
                        <div class="p-6 hover:bg-gray-50/50 transition-colors">
                            <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[10px] font-bold bg-blue-50 text-blue-600 px-2 py-0.5 rounded"><?= h($ins['materia_codigo']) ?></span>
                                        <h4 class="text-sm font-bold text-gray-900"><?= h($ins['materia_nombre']) ?></h4>
                                    </div>
                                    <p class="text-xs text-gray-500 flex items-center gap-2">
                                        <i class="fas fa-chalkboard-teacher text-[10px]"></i>
                                        Prof. <?= h($ins['profesor_nombre']) ?> | Sección: <?= h($ins['seccion']) ?>
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <?php
                                    $promedio = 0;
                                    $count = count($ins['notas']);
                                    if ($count > 0) {
                                        $suma = array_sum(array_column($ins['notas'], 'nota'));
                                        $promedio = $suma / $count;
                                    }
                                    ?>
                                    <div class="text-right">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase">Promedio</p>
                                        <p class="text-lg font-black <?= $promedio >= 70 ? 'text-green-600' : 'text-red-600' ?>">
                                            <?= number_format($promedio, 1) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <?php foreach ($ins['notas'] as $nota): ?>
                                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                                        <p class="text-[9px] font-bold text-gray-400 uppercase truncate"><?= h($nota['periodo_nombre']) ?></p>
                                        <p class="text-sm font-black text-gray-900 mt-0.5"><?= number_format($nota['nota'], 0) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
