<div class="max-w-4xl mx-auto" x-data="userWizard()">
    <!-- Selector de Tipo de Registro -->
    <div class="flex gap-4 mb-8" x-show="step === 'choice'">
        <button @click="setRole('administrador')" class="flex-1 bg-white p-8 rounded-2xl shadow-sm border-2 border-transparent hover:border-blue-500 transition-all text-center group">
            <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                <i class="fas fa-user-shield text-2xl text-blue-600"></i>
            </div>
            <h3 class="font-bold text-gray-900">Registrar Administrador</h3>
            <p class="text-xs text-gray-400 mt-2">Acceso total al sistema y gestión de configuraciones.</p>
        </button>

        <button @click="setRole('profesor')" class="flex-1 bg-white p-8 rounded-2xl shadow-sm border-2 border-transparent hover:border-purple-500 transition-all text-center group">
            <div class="w-16 h-16 bg-purple-50 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                <i class="fas fa-chalkboard-teacher text-2xl text-purple-600"></i>
            </div>
            <h3 class="font-bold text-gray-900">Registrar Profesor</h3>
            <p class="text-xs text-gray-400 mt-2">Wizard paso a paso para datos personales y materias.</p>
        </button>
    </div>

    <!-- Wizard Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" x-show="step !== 'choice'" x-cloak>
        <!-- Header -->
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-900" x-text="isProfessor ? 'Wizard: Registro de Profesor' : 'Registro de Administrador'"></h3>
                <p class="text-xs text-gray-400" x-show="isProfessor" x-text="'Paso ' + currentStep + ' de 2: ' + steps[currentStep-1]"></p>
            </div>
            <button @click="step = 'choice'; reset()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>

        <form action="<?= APP_URL ?>/usuarios/store" method="POST" class="p-8 space-y-6" id="userForm">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= Session::get(CSRF_TOKEN_NAME) ?>">
            <input type="hidden" name="rol_id" :value="selectedRoleId">

            <!-- PASO 1: Datos Personales (Común) -->
            <div x-show="currentStep === 1" class="space-y-6 animate-fade-in">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Nombre Completo</label>
                        <input type="text" name="nombre" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                            placeholder="Ej: Juan Pérez">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Correo Electrónico</label>
                        <input type="email" name="email" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                            placeholder="usuario@arcoiris.edu.ni">
                    </div>

                    <div x-show="isProfessor">
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Teléfono</label>
                        <input type="text" name="telefono"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                            placeholder="Ej: 8888 8888">
                    </div>

                    <div :class="isProfessor ? '' : 'md:col-span-2'">
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Contraseña</label>
                        <div class="relative" x-data="{ show: false }">
                            <input :type="show ? 'text' : 'password'" name="password" id="password_input" required
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                                placeholder="Mínimo 8 caracteres">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 flex gap-2">
                                <button type="button" @click="show = !show" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                                <button type="button" @click="generatePassword(); show = true" class="text-[10px] bg-blue-600 text-white px-2 py-1 rounded font-bold hover:bg-blue-700 transition-colors">
                                    Generar
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="confirm_password" id="confirm_password_input">
                    </div>

                    <div x-show="isProfessor" class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Especialidad</label>
                        <input type="text" name="especialidad"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                            placeholder="Ej: Matemáticas, Ciencias Naturales">
                    </div>
                </div>
            </div>

            <!-- PASO 2: Materia (Solo Profesores) -->
            <div x-show="currentStep === 2" class="space-y-6 animate-fade-in">
                <div class="bg-purple-50 p-6 rounded-2xl border border-purple-100">
                    <h4 class="text-sm font-bold text-purple-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-book"></i> Asignación de Materia Inicial
                    </h4>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-bold text-purple-400 uppercase mb-1">Seleccionar Materia Existente</label>
                            <select name="materia_id" x-model="selectedMateria"
                                class="w-full px-4 py-3 bg-white border border-purple-200 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all outline-none">
                                <option value="">-- No asignar aún --</option>
                                <?php foreach ($materias as $mat): ?>
                                    <option value="<?= $mat['id'] ?>"><?= h($mat['nombre']) ?> (<?= h($mat['grado_nombre']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="relative py-4">
                            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-purple-200"></div></div>
                            <div class="relative flex justify-center text-[10px] uppercase font-bold"><span class="bg-purple-50 px-2 text-purple-400">O crear una nueva</span></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-bold text-purple-400 uppercase mb-1">Nombre de Nueva Materia</label>
                                <input type="text" name="new_materia_nombre" x-model="newMateriaNombre" @input="suggestNewCode()"
                                    class="w-full px-4 py-2 bg-white border border-purple-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none text-sm"
                                    placeholder="Ej: Física Cuántica">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-purple-400 uppercase mb-1">Nivel</label>
                                <select name="new_materia_nivel" x-model="newMateriaNivel" @change="loadGrados()"
                                    class="w-full px-4 py-2 bg-white border border-purple-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none text-sm">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($niveles as $niv): ?>
                                        <option value="<?= $niv['id'] ?>"><?= h($niv['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-purple-400 uppercase mb-1">Grado</label>
                                <select name="new_materia_grado" x-model="newMateriaGrado"
                                    class="w-full px-4 py-2 bg-white border border-purple-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none text-sm">
                                    <template x-for="g in grados" :key="g.id">
                                        <option :value="g.id" x-text="g.nombre"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-purple-400 uppercase mb-1">Código Sugerido</label>
                                <input type="text" name="new_materia_codigo" x-model="newMateriaCodigo"
                                    class="w-full px-4 py-2 bg-white border border-purple-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-purple-400 uppercase mb-1">Sección</label>
                                <input type="text" name="seccion" value="A"
                                    class="w-full px-4 py-2 bg-white border border-purple-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none text-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="activo" value="1" id="activo" checked class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                    <label for="activo" class="text-sm font-medium text-gray-700">Usuario Activo</label>
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="prev()" x-show="currentStep > 1" class="px-6 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all">Anterior</button>
                    <button type="button" @click="next()" class="bg-blue-600 text-white px-8 py-3 rounded-xl text-sm font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">
                        <span x-text="isProfessor ? (currentStep === 1 ? 'Siguiente' : 'Finalizar Registro') : 'Guardar Administrador'"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function userWizard() {
    const roles = <?= json_encode($roles) ?>;
    const adminRole = roles.find(r => r.nombre.toLowerCase() === 'administrador');
    const profRole = roles.find(r => r.nombre.toLowerCase() === 'profesor');

    return {
        step: 'choice',
        currentStep: 1,
        isProfessor: false,
        selectedRoleId: '',
        steps: ['Datos Personales', 'Asignación de Materia'],
        grados: [],
        selectedMateria: '',
        newMateriaNombre: '',
        newMateriaNivel: '',
        newMateriaGrado: '',
        newMateriaCodigo: '',

        setRole(role) {
            this.isProfessor = (role === 'profesor');
            this.selectedRoleId = this.isProfessor ? profRole.id : adminRole.id;
            this.step = 'wizard';
            this.currentStep = 1;
        },

        async loadGrados() {
            if (!this.newMateriaNivel) return;
            const response = await fetch(`<?= APP_URL ?>/materias/apiGrados/${this.newMateriaNivel}`);
            this.grados = await response.json();
        },

        async suggestNewCode() {
            if (this.newMateriaNombre.length < 3) return;
            const response = await fetch(`<?= APP_URL ?>/materias/apiSugerirCodigo?nombre=${encodeURIComponent(this.newMateriaNombre)}`);
            const data = await response.json();
            this.newMateriaCodigo = data.codigo;
        },

        generatePassword() {
            const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
            let retVal = "";
            for (let i = 0, n = charset.length; i < 12; ++i) {
                retVal += charset.charAt(Math.floor(Math.random() * n));
            }
            document.getElementById('password_input').value = retVal;
            document.getElementById('confirm_password_input').value = retVal;
        },

        next() {
            if (this.isProfessor && this.currentStep === 1) {
                // Validar paso 1
                const form = document.getElementById('userForm');
                if (!form.nombre.value || !form.email.value || !form.password.value) {
                    alert('Por favor complete los campos obligatorios.');
                    return;
                }
                document.getElementById('confirm_password_input').value = document.getElementById('password_input').value;
                this.currentStep = 2;
            } else {
                document.getElementById('confirm_password_input').value = document.getElementById('password_input').value;
                document.getElementById('userForm').submit();
            }
        },

        prev() {
            if (this.currentStep > 1) this.currentStep--;
        },

        reset() {
            this.currentStep = 1;
            this.isProfessor = false;
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
</style>
