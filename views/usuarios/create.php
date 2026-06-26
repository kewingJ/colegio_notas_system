<div class="max-w-4xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-bold text-gray-900">Datos del Nuevo Usuario</h3>
        </div>
        <form action="<?= APP_URL ?>/usuarios/store" method="POST" class="p-8 space-y-6">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= Session::get(CSRF_TOKEN_NAME) ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Nombre Completo</label>
                    <input type="text" name="nombre" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                        placeholder="Ej: Juan Pérez">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Correo Electrónico</label>
                    <input type="email" name="email" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                        placeholder="usuario@arcoiris.edu.ni">
                </div>

                <!-- Rol -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Rol del Sistema</label>
                    <select name="rol_id" id="rol_select" required onchange="toggleTeacherFields()"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= ucfirst($r['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Contraseña</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                        placeholder="Mínimo 8 caracteres">
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Confirmar Contraseña</label>
                    <input type="password" name="confirm_password" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                        placeholder="Repite la contraseña">
                </div>
            </div>

            <!-- Campos adicionales para Profesores -->
            <div id="teacher_fields" class="hidden pt-6 border-t border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-6 animate-fade-in">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Teléfono</label>
                    <input type="text" name="telefono"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                        placeholder="Ej: 8888 8888">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Especialidad</label>
                    <input type="text" name="especialidad"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                        placeholder="Ej: Matemáticas, Ciencias">
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="activo" value="1" id="activo" checked class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                    <label for="activo" class="text-sm font-medium text-gray-700">Usuario Activo</label>
                </div>
                <div class="flex gap-3">
                    <a href="<?= APP_URL ?>/usuarios" class="px-6 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all">Cancelar</a>
                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl text-sm font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">
                        Guardar Usuario
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function toggleTeacherFields() {
    const roleSelect = document.getElementById('rol_select');
    const teacherFields = document.getElementById('teacher_fields');
    // Asumimos que el ID 2 es para Profesor basado en el seeder
    const selectedText = roleSelect.options[roleSelect.selectedIndex].text.toLowerCase();

    if (selectedText.includes('profesor')) {
        teacherFields.classList.remove('hidden');
    } else {
        teacherFields.classList.add('hidden');
    }
}
// Ejecutar al cargar por si acaso
toggleTeacherFields();
</script>

<style>
@keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
</style>
