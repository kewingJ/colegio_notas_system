<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($asignaciones as $asig): ?>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all overflow-hidden group">
        <div class="p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="bg-blue-50 text-blue-600 w-12 h-12 rounded-xl flex items-center justify-center text-xl font-black group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <i class="fas fa-book"></i>
                </div>
                <span class="bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-1 rounded">Sección <?= h($asig['seccion']) ?></span>
            </div>

            <h4 class="text-lg font-black text-gray-900 leading-tight mb-1"><?= h($asig['materia_nombre']) ?></h4>
            <p class="text-xs font-bold text-blue-600 mb-4"><?= h($asig['codigo']) ?></p>

            <div class="space-y-2 mb-6">
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <i class="fas fa-layer-group w-4"></i>
                    <span><?= h($asig['nivel_nombre']) ?></span>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <i class="fas fa-graduation-cap w-4"></i>
                    <span><?= h($asig['grado_nombre']) ?></span>
                </div>
                <?php if (Session::get('role_name') === 'administrador'): ?>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <i class="fas fa-chalkboard-teacher w-4"></i>
                    <span><?= h($asig['profesor_nombre']) ?></span>
                </div>
                <?php endif; ?>
            </div>

            <a href="<?= APP_URL ?>/calificaciones/gestionar/<?= $asig['id'] ?>" class="flex items-center justify-center gap-2 w-full py-3 bg-gray-900 text-white rounded-xl text-xs font-bold hover:bg-blue-600 transition-all">
                <i class="fas fa-edit"></i> Gestionar Notas
            </a>
        </div>
    </div>
    <?php endforeach; ?>

    <?php if (empty($asignaciones)): ?>
        <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-dashed border-gray-200">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-clipboard-list text-gray-300 text-xl"></i>
            </div>
            <h3 class="text-gray-900 font-bold">No hay materias asignadas</h3>
            <p class="text-gray-500 text-sm mt-1">No se encontraron materias bajo tu responsabilidad para calificar.</p>
        </div>
    <?php endif; ?>
</div>
