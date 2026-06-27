<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h3 class="text-lg font-black text-gray-900"><?= h($pm['materia_nombre']) ?></h3>
            <p class="text-xs text-gray-500 font-medium"><?= h($pm['nivel_nombre']) ?> - <?= h($pm['grado_nombre']) ?> | Sección <?= h($pm['seccion']) ?></p>
        </div>
        <a href="<?= APP_URL ?>/materias" class="text-xs font-bold text-gray-500 hover:text-gray-900 transition-all">
            <i class="fas fa-arrow-left mr-1"></i> Volver a Materias
        </a>
    </div>

    <form action="<?= APP_URL ?>/materias/doEnroll" method="POST" class="p-6">
        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= Session::get(CSRF_TOKEN_NAME) ?>">
        <input type="hidden" name="pm_id" value="<?= $pm['id'] ?>">
        <input type="hidden" name="anio_lectivo" value="<?= $pm['anio_lectivo'] ?>">

        <div class="mb-6 bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3">
            <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
            <p class="text-xs text-blue-700 leading-relaxed">
                A continuación se muestran los alumnos matriculados en <strong><?= h($pm['nivel_nombre']) ?> / <?= h($pm['grado_nombre']) ?></strong> obtenidos desde el sistema central. Selecciona los alumnos que deseas inscribir en esta materia específica.
            </p>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div class="flex gap-3">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-search text-xs"></i>
                    </span>
                    <input type="text" id="alumnoSearch" value="<?= h($search) ?>" placeholder="Buscar por nombre o carnet..."
                        class="pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                </div>
                <button type="button" onclick="applyFilters()" class="bg-gray-800 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-gray-900 transition-all">
                    Filtrar
                </button>
            </div>

            <?php if (!empty($alumnos)): ?>
                <div class="flex items-center gap-4">
                    <div id="selection-counter" class="text-xs font-bold text-gray-500 hidden">
                        <span id="selected-count">0</span> seleccionados
                    </div>

                    <div class="flex gap-2">
                        <button type="button" onclick="selectAllResults()" class="px-4 py-3 bg-gray-100 text-gray-600 rounded-xl text-xs font-bold hover:bg-gray-200 transition-all">
                            Marcar Todos los Resultados
                        </button>
                        <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl text-sm font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all flex items-center gap-2">
                            <i class="fas fa-user-plus"></i> Procesar Inscripción
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-widest text-gray-400 font-bold">
                        <th class="px-6 py-4 w-10">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-4">Carnet</th>
                        <th class="px-6 py-4">Estudiante</th>
                        <th class="px-6 py-4">Estado en API</th>
                        <th class="px-6 py-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($alumnos as $a): ?>
                    <tr class="hover:bg-gray-50/50 transition-colors <?= $a['ya_inscrito'] ? 'opacity-50' : '' ?>">
                        <td class="px-6 py-4 text-center">
                            <?php if ($a['ya_inscrito']): ?>
                                <i class="fas fa-check-circle text-green-500" title="Ya inscrito"></i>
                            <?php else: ?>
                                <input type="checkbox" name="alumnos[]" value="<?= h($a['carnet']) ?>" class="student-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 font-mono text-xs font-bold text-gray-600"><?= h($a['carnet']) ?></td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900"><?= h($a['nombre']) ?></td>
                        <td class="px-6 py-4">
                            <span class="text-[10px] font-black uppercase text-green-600 bg-green-50 px-2 py-1 rounded"><?= h($a['estado']) ?></span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php if ($a['ya_inscrito']): ?>
                                <button type="button" onclick="unenroll('<?= h($a['carnet']) ?>', '<?= h(addslashes($a['nombre'])) ?>')" class="text-xs font-bold text-red-500 hover:text-red-700 transition-colors" title="Quitar inscripción">
                                    <i class="fas fa-user-minus mr-1"></i> Quitar
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (empty($alumnos)): ?>
            <div class="py-12 text-center">
                <i class="fas fa-user-slash text-gray-300 text-3xl mb-4 block"></i>
                <p class="text-gray-500 text-sm">No se encontraron alumnos bajo los criterios de búsqueda.</p>
            </div>
        <?php endif; ?>
    </form>

    <?php require_once __DIR__ . '/../partials/pagination.php'; ?>
</div>

<form id="unenrollForm" action="<?= APP_URL ?>/materias/doUnenroll" method="POST" style="display: none;">
    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= Session::get(CSRF_TOKEN_NAME) ?>">
    <input type="hidden" name="pm_id" value="<?= $pm['id'] ?>">
    <input type="hidden" name="materia_id" value="<?= $pm['materia_id'] ?>">
    <input type="hidden" name="anio_lectivo" value="<?= $pm['anio_lectivo'] ?>">
    <input type="hidden" name="carnet" id="unenroll_carnet" value="">
</form>

<script>
// Persistencia de selección entre páginas
let selectedAlumnos = JSON.parse(localStorage.getItem('selected_enroll_alumnos_<?= $pm['id'] ?>') || '[]');

function updateSelectionDisplay() {
    const counter = document.getElementById('selection-counter');
    const countSpan = document.getElementById('selected-count');
    if (selectedAlumnos.length > 0) {
        counter.classList.remove('hidden');
        countSpan.textContent = selectedAlumnos.length;
    } else {
        counter.classList.add('hidden');
    }

    // Actualizar campos ocultos en el formulario para envío
    const form = document.querySelector('form[action$="doEnroll"]');
    // Limpiar anteriores
    form.querySelectorAll('input[name="alumnos[]"]').forEach(i => {
        if (!i.classList.contains('student-checkbox')) i.remove();
    });
    // Añadir seleccionados que no estén en la página actual
    selectedAlumnos.forEach(carnet => {
        const visibleCheckbox = form.querySelector(`.student-checkbox[value="${carnet}"]`);
        if (!visibleCheckbox) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'alumnos[]';
            input.value = carnet;
            form.appendChild(input);
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Marcar checkboxes basados en localStorage
    document.querySelectorAll('.student-checkbox').forEach(cb => {
        if (selectedAlumnos.includes(cb.value)) {
            cb.checked = true;
        }
        cb.addEventListener('change', (e) => {
            if (e.target.checked) {
                if (!selectedAlumnos.includes(e.target.value)) selectedAlumnos.push(e.target.value);
            } else {
                selectedAlumnos = selectedAlumnos.filter(id => id !== e.target.value);
            }
            localStorage.setItem('selected_enroll_alumnos_<?= $pm['id'] ?>', JSON.stringify(selectedAlumnos));
            updateSelectionDisplay();
        });
    });
    updateSelectionDisplay();
});

// Limpiar localStorage al enviar
document.querySelector('form[action$="doEnroll"]').addEventListener('submit', () => {
    localStorage.removeItem('selected_enroll_alumnos_<?= $pm['id'] ?>');
});

function selectAllResults() {
    if (confirm('¿Desea seleccionar todos los alumnos que coinciden con la búsqueda actual en todas las páginas?')) {
        const form = document.querySelector('form[action$="doEnroll"]');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'select_all_results';
        input.value = '1';
        form.appendChild(input);
        form.submit();
    }
}

function applyFilters() {
    const search = document.getElementById('alumnoSearch').value;
    const url = new URL(window.location.href);
    url.searchParams.set('search', search);
    url.searchParams.set('page', 1);
    window.location.href = url.toString();
}

document.getElementById('alumnoSearch').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        applyFilters();
    }
});

document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.student-checkbox:not(:disabled)');
    checkboxes.forEach(cb => {
        cb.checked = this.checked;
        // Disparar evento change manualmente para actualizar localStorage
        cb.dispatchEvent(new Event('change'));
    });
});

function unenroll(carnet, nombre) {
    if (confirm(`¿Está seguro que desea quitar la inscripción de ${nombre} (${carnet})? Se perderán las calificaciones asociadas a esta materia.`)) {
        document.getElementById('unenroll_carnet').value = carnet;
        document.getElementById('unenrollForm').submit();
    }
}
</script>
