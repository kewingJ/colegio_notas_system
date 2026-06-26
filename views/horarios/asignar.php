<div class="max-w-xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-bold text-gray-900">Agregar Bloque de Horario</h3>
            <p class="text-xs text-gray-500 mt-1">Profesor: <span class="font-bold text-gray-700"><?= h($profesorNombre) ?></span></p>
        </div>
        <form action="<?= APP_URL ?>/horarios/store" method="POST" class="p-8 space-y-6">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= Session::get(CSRF_TOKEN_NAME) ?>">
            <input type="hidden" name="profesor_id" value="<?= $profesor_id ?>">
            <input type="hidden" name="anio_lectivo" value="<?= $anioActivo ?>">

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Materia Asignada</label>
                <select name="materia_id" required onchange="setSeccion(this)"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                    <option value="">Seleccione materia...</option>
                    <?php foreach ($materias as $m): ?>
                        <option value="<?= $m['id'] ?>" data-seccion="<?= $m['seccion'] ?>"><?= h($m['nombre']) ?> (<?= h($m['codigo']) ?>) - Sec. <?= $m['seccion'] ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="seccion" id="seccion_input">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Día de la Semana</label>
                    <select name="dia_semana" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                        <option value="1" <?= $dia == 1 ? 'selected' : '' ?>>Lunes</option>
                        <option value="2" <?= $dia == 2 ? 'selected' : '' ?>>Martes</option>
                        <option value="3" <?= $dia == 3 ? 'selected' : '' ?>>Miércoles</option>
                        <option value="4" <?= $dia == 4 ? 'selected' : '' ?>>Jueves</option>
                        <option value="5" <?= $dia == 5 ? 'selected' : '' ?>>Viernes</option>
                        <option value="6" <?= $dia == 6 ? 'selected' : '' ?>>Sábado</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Aula / Salón</label>
                    <input type="text" name="aula" placeholder="Ej: Aula 204"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Hora Inicio</label>
                    <input type="time" name="hora_inicio" value="<?= $hora ?>" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Hora Fin</label>
                    <input type="time" name="hora_fin" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex gap-3 justify-end">
                <a href="<?= APP_URL ?>/horarios?profesor_id=<?= $profesor_id ?>" class="px-6 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all">Cancelar</a>
                <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl text-sm font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">
                    Guardar Bloque
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function setSeccion(select) {
    const option = select.options[select.selectedIndex];
    document.getElementById('seccion_input').value = option.dataset.seccion;
}
</script>
