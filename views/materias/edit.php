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

            <div class="mt-8 border-t border-gray-100 pt-8">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-black text-gray-900 uppercase tracking-wider">Configuración de Evaluaciones</h4>
                    <button type="button" onclick="addEvaluationRow()" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                        <i class="fas fa-plus mr-1"></i> Añadir Evaluación
                    </button>
                </div>
                <div id="evaluations-container" class="space-y-3">
                    <?php if (empty($evaluaciones)): ?>
                        <div class="grid grid-cols-12 gap-4 items-center bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <div class="col-span-7">
                                <input type="text" name="evaluaciones[0][nombre]" value="I Parcial" placeholder="Nombre" class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm outline-none">
                            </div>
                            <div class="col-span-4">
                                <div class="relative">
                                    <input type="number" name="evaluaciones[0][peso]" value="35" placeholder="Peso" class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm outline-none pr-8">
                                    <span class="absolute inset-y-0 right-3 flex items-center text-gray-400 text-xs">%</span>
                                </div>
                            </div>
                            <div class="col-span-1 text-center">
                                <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-red-500"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($evaluaciones as $idx => $eval): ?>
                            <div class="grid grid-cols-12 gap-4 items-center bg-gray-50 p-3 rounded-xl border border-gray-100">
                                <div class="col-span-7">
                                    <input type="text" name="evaluaciones[<?= $idx ?>][nombre]" value="<?= h($eval['nombre']) ?>" placeholder="Nombre" class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm outline-none">
                                </div>
                                <div class="col-span-4">
                                    <div class="relative">
                                        <input type="number" name="evaluaciones[<?= $idx ?>][peso]" value="<?= (int)$eval['peso_porcentaje'] ?>" placeholder="Peso" class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm outline-none pr-8">
                                        <span class="absolute inset-y-0 right-3 flex items-center text-gray-400 text-xs">%</span>
                                    </div>
                                </div>
                                <div class="col-span-1 text-center">
                                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-red-500"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
let evalIndex = <?= count($evaluaciones) ?: 1 ?>;
function addEvaluationRow() {
    const container = document.getElementById('evaluations-container');
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-4 items-center bg-gray-50 p-3 rounded-xl border border-gray-100';
    row.innerHTML = `
        <div class="col-span-7">
            <input type="text" name="evaluaciones[${evalIndex}][nombre]" placeholder="Nombre" class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm outline-none">
        </div>
        <div class="col-span-4">
            <div class="relative">
                <input type="number" name="evaluaciones[${evalIndex}][peso]" placeholder="Peso" class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm outline-none pr-8">
                <span class="absolute inset-y-0 right-3 flex items-center text-gray-400 text-xs">%</span>
            </div>
        </div>
        <div class="col-span-1 text-center">
            <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-red-500"><i class="fas fa-trash-alt"></i></button>
        </div>
    `;
    container.appendChild(row);
    evalIndex++;
}

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
