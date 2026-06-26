<div class="max-w-4xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-bold text-gray-900">Editar Materia: <?= h($materia['nombre']) ?></h3>
        </div>
        <form action="<?= APP_URL ?>/materias/update/<?= $materia['id'] ?>" method="POST" class="p-8 space-y-6">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= Session::get(CSRF_TOKEN_NAME) ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Nombre de la Materia</label>
                    <input type="text" name="nombre" value="<?= h($materia['nombre']) ?>" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Nivel Académico</label>
                    <select name="nivel_id" id="nivel_select" required onchange="loadGrados()"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                        <?php foreach ($niveles as $n): ?>
                            <option value="<?= $n['id'] ?>" <?= $materia['nivel_id'] == $n['id'] ? 'selected' : '' ?>><?= h($n['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Grado</label>
                    <select name="grado_id" id="grado_select" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                        <?php foreach ($grados as $g): ?>
                            <option value="<?= $g['id'] ?>" <?= $materia['grado_id'] == $g['id'] ? 'selected' : '' ?>><?= h($g['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Código</label>
                    <input type="text" name="codigo" value="<?= h($materia['codigo']) ?>" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Cupo Máximo</label>
                    <input type="number" name="cupo_maximo" value="<?= $materia['cupo_maximo'] ?>" min="0"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Descripción</label>
                    <textarea name="descripcion" rows="3"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none"><?= h($materia['descripcion']) ?></textarea>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="activa" value="1" id="activa" <?= $materia['activa'] ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                    <label for="activa" class="text-sm font-medium text-gray-700">Materia Activa</label>
                </div>
                <div class="flex gap-3">
                    <a href="<?= APP_URL ?>/materias" class="px-6 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all">Cancelar</a>
                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl text-sm font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">
                        Actualizar Materia
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
async function loadGrados() {
    const nivelId = document.getElementById('nivel_select').value;
    const gradoSelect = document.getElementById('grado_select');
    const response = await fetch(`<?= APP_URL ?>/materias/apiGrados/${nivelId}`);
    const grados = await response.json();
    gradoSelect.innerHTML = '';
    grados.forEach(g => {
        gradoSelect.innerHTML += `<option value="${g.id}">${g.nombre}</option>`;
    });
}
</script>
