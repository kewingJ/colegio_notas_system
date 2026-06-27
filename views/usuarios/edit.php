<div class="max-w-4xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-bold text-gray-900">Editar Usuario: <?= h($user['nombre']) ?></h3>
        </div>
        <form action="<?= APP_URL ?>/usuarios/update/<?= $user['id'] ?>" method="POST" class="p-8 space-y-6">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= Session::get(CSRF_TOKEN_NAME) ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Nombre Completo</label>
                    <input type="text" name="nombre" value="<?= h($user['nombre']) ?>" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Correo Electrónico</label>
                    <input type="email" name="email" value="<?= h($user['email']) ?>" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Rol del Sistema</label>
                    <select name="rol_id" id="rol_select" required onchange="toggleTeacherFields()"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= $user['rol_id'] == $r['id'] ? 'selected' : '' ?>><?= ucfirst($r['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Contraseña (opcional)</label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" name="password" id="password_input"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                            placeholder="Dejar en blanco para no cambiar">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 flex gap-2">
                            <button type="button" @click="show = !show" class="text-gray-400 hover:text-gray-600">
                                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                            <button type="button" onclick="generatePassword()" class="text-[10px] bg-blue-600 text-white px-2 py-1 rounded font-bold hover:bg-blue-700 transition-colors">
                                Generar
                            </button>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Confirmar Contraseña</label>
                    <input type="password" name="confirm_password" id="confirm_password_input"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                        placeholder="Repite la contraseña">
                </div>
            </div>

            <div id="teacher_fields" class="<?= $user['rol_nombre'] === 'profesor' ? '' : 'hidden' ?> pt-6 border-t border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-6 animate-fade-in">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Teléfono</label>
                    <input type="text" name="telefono" value="<?= h($user['telefono'] ?? '') ?>"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                        placeholder="Ej: 8888 8888">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Especialidad</label>
                    <input type="text" name="especialidad" value="<?= h($user['especialidad'] ?? '') ?>"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                        placeholder="Ej: Matemáticas, Ciencias">
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="activo" value="1" id="activo" <?= $user['activo'] ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                    <label for="activo" class="text-sm font-medium text-gray-700">Usuario Activo</label>
                </div>
                <div class="flex gap-3">
                    <a href="<?= APP_URL ?>/usuarios" class="px-6 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all">Cancelar</a>
                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl text-sm font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">
                        Actualizar Usuario
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function generatePassword() {
    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
    let retVal = "";
    for (let i = 0, n = charset.length; i < 12; ++i) {
        retVal += charset.charAt(Math.floor(Math.random() * n));
    }
    document.getElementById('password_input').value = retVal;
    document.getElementById('confirm_password_input').value = retVal;
}

function toggleTeacherFields() {
    const roleSelect = document.getElementById('rol_select');
    const teacherFields = document.getElementById('teacher_fields');
    const selectedText = roleSelect.options[roleSelect.selectedIndex].text.toLowerCase();

    if (selectedText.includes('profesor')) {
        teacherFields.classList.remove('hidden');
    } else {
        teacherFields.classList.add('hidden');
    }
}
</script>
