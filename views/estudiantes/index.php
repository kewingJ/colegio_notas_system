<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex flex-wrap gap-4 items-center justify-between">
        <form action="<?= APP_URL ?>/estudiantes" method="GET" class="flex flex-wrap gap-3 items-center">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input type="text" name="search" value="<?= h($search) ?>" placeholder="Buscar por carnet o nombre..."
                    class="pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none">
            </div>

            <select name="nivel" onchange="this.form.submit()" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white outline-none">
                <option value="">Todos los Niveles</option>
                <?php foreach ($niveles as $n): ?>
                    <option value="<?= $n['nombre'] ?>" <?= $nivel == $n['nombre'] ? 'selected' : '' ?>><?= h($n['nombre']) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-gray-900 transition-all">
                Filtrar
            </button>
        </form>

        <div class="text-sm text-gray-500">
            Total Alumnos: <span class="font-bold text-gray-900"><?= $total ?></span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
        <?php foreach ($estudiantes as $e): ?>
        <div class="bg-white border border-gray-100 rounded-2xl p-5 hover:shadow-md transition-all group">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-black group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <?= substr($e['nombre'], 0, 1) ?>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-900 leading-tight"><?= h($e['nombre']) ?></h4>
                        <p class="text-[10px] font-mono font-bold text-blue-600"><?= h($e['carnet']) ?></p>
                    </div>
                </div>
                <span class="px-2 py-1 rounded text-[9px] font-black uppercase <?= $e['estado'] === 'Matriculado e Inscrito' ? 'bg-green-50 text-green-600' : 'bg-gray-50 text-gray-400' ?>">
                    <?= h($e['estado']) ?>
                </span>
            </div>

            <div class="space-y-2 mb-5">
                <div class="flex items-center gap-2 text-xs text-gray-600">
                    <i class="fas fa-graduation-cap w-4 text-gray-400"></i>
                    <span><?= h($e['academico']['grado']) ?> (<?= h($e['academico']['seccion']) ?>)</span>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-600">
                    <i class="fas fa-layer-group w-4 text-gray-400"></i>
                    <span><?= h($e['academico']['nivel']) ?></span>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-600">
                    <i class="fas fa-user-friends w-4 text-gray-400"></i>
                    <span class="truncate">Padre: <?= h($e['padre']) ?></span>
                </div>
            </div>

            <a href="<?= APP_URL ?>/estudiantes/show/<?= $e['carnet'] ?>" class="block w-full text-center py-2 bg-gray-50 text-gray-600 rounded-xl text-xs font-bold hover:bg-blue-600 hover:text-white transition-all">
                Ver Expediente Completo
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <?php require_once __DIR__ . '/../../views/partials/pagination.php'; ?>
</div>
