<div class="max-w-2xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-bold text-gray-900">Asignar Profesor a Materia</h3>
            <p class="text-xs text-gray-500 mt-1"><?= h($materia['nombre']) ?> (<?= h($materia['codigo']) ?>)</p>
        </div>
        <form action="<?= APP_URL ?>/materias/doAssign/<?= $materia['id'] ?>" method="POST" class="p-8 space-y-6">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= Session::get(CSRF_TOKEN_NAME) ?>">

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Profesor Responsable</label>
                <select name="profesor_id" required
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                    <option value="">Seleccione un profesor</option>
                    <?php foreach ($profesores as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= h($p['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Año Lectivo</label>
                    <input type="number" name="anio_lectivo" value="<?= $anioActivo ?>" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Sección</label>
                    <select name="seccion" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                        <option value="A">Sección A</option>
                        <option value="B">Sección B</option>
                        <option value="C">Sección C</option>
                        <option value="D">Sección D</option>
                    </select>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex gap-3 justify-end">
                <a href="<?= APP_URL ?>/materias" class="px-6 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all">Cancelar</a>
                <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl text-sm font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">
                    Confirmar Asignación
                </button>
            </div>
        </form>
    </div>
</div>
