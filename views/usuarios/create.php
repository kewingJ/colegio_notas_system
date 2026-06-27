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
        <div class="p-6 border-b border-gray-100">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="font-bold text-gray-900" x-text="isProfessor ? 'Wizard: Registro de Profesor' : 'Registro de Administrador'"></h3>
                    <p class="text-xs text-gray-400" x-show="isProfessor" x-text="'Paso ' + currentStep + ' de 2: ' + steps[currentStep-1]"></p>
                </div>
                <button @click="step = 'choice'; reset()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            
            <!-- Stepper Visual -->
            <div x-show="isProfessor" class="flex items-center w-full max-w-sm mx-auto">
                <div class="flex items-center relative">
                    <div :class="currentStep >= 1 ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'bg-gray-100 text-gray-400'" class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all relative z-10">1</div>
                    <div class="absolute top-12 left-1/2 -translate-x-1/2 text-[10px] font-bold text-gray-400 whitespace-nowrap">Datos Personales</div>
                </div>
                <div :class="currentStep >= 2 ? 'bg-blue-600' : 'bg-gray-100'" class="flex-1 h-1 mx-2 transition-all rounded"></div>
                <div class="flex items-center relative">
                    <div :class="currentStep >= 2 ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'bg-gray-100 text-gray-400'" class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all relative z-10">2</div>
                    <div class="absolute top-12 left-1/2 -translate-x-1/2 text-[10px] font-bold text-gray-400 whitespace-nowrap">Materias</div>
                </div>
            </div>
            <div x-show="isProfessor" class="h-6"></div> <!-- spacer para labels -->
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
                        <i class="fas fa-list"></i> Asignación de Materias
                    </h4>

                    <!-- Tipo de asignación -->
                    <div class="flex gap-4 mb-4">
                        <label class="flex items-center gap-2 text-xs font-bold text-purple-900 cursor-pointer">
                            <input type="radio" x-model="assignmentType" value="existing" class="text-purple-600 focus:ring-purple-500">
                            Asignar Existente
                        </label>
                        <label class="flex items-center gap-2 text-xs font-bold text-purple-900 cursor-pointer">
                            <input type="radio" x-model="assignmentType" value="new" class="text-purple-600 focus:ring-purple-500">
                            Crear Nueva
                        </label>
                    </div>

                    <div class="bg-white p-4 rounded-xl border border-purple-100 shadow-sm mb-6">
                        <!-- Campos para Materia Existente -->
                        <div x-show="assignmentType === 'existing'" class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-bold text-purple-400 uppercase mb-1">Materia</label>
                                <select x-model="selectedMateria" x-ref="materiaSelect"
                                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 transition-all outline-none text-sm">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($materias as $mat): ?>
                                        <option value="<?= $mat['id'] ?>" data-name="<?= h($mat['nombre']) ?> (<?= h($mat['grado_nombre']) ?>)"><?= h($mat['nombre']) ?> (<?= h($mat['grado_nombre']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Campos para Nueva Materia -->
                        <div x-show="assignmentType === 'new'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-bold text-purple-400 uppercase mb-1">Nombre</label>
                                <input type="text" x-model="newMateriaNombre" @input="suggestNewCode()"
                                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none text-sm"
                                    placeholder="Ej: Física Cuántica">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-purple-400 uppercase mb-1">Nivel</label>
                                <select x-model="newMateriaNivel" @change="loadGrados()" x-ref="nivelSelect"
                                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none text-sm">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($niveles as $niv): ?>
                                        <option value="<?= $niv['id'] ?>" data-name="<?= h($niv['nombre']) ?>"><?= h($niv['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-purple-400 uppercase mb-1">Grado</label>
                                <select x-model="newMateriaGrado" x-ref="gradoSelect"
                                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none text-sm">
                                    <option value="">Seleccione...</option>
                                    <template x-for="g in grados" :key="g.id">
                                        <option :value="g.id" x-text="g.nombre" :data-name="g.nombre"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-purple-400 uppercase mb-1">Código Sugerido</label>
                                <input type="text" x-model="newMateriaCodigo"
                                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-purple-400 uppercase mb-1">Cupo Máximo (0=Ilimitado)</label>
                                <input type="number" x-model="newMateriaCupo" min="0"
                                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none text-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-bold text-purple-400 uppercase mb-1">Descripción</label>
                                <textarea x-model="newMateriaDesc" rows="2"
                                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none text-sm"></textarea>
                            </div>
                        </div>

                        <!-- Campos Comunes (Sección y Año) -->
                        <div class="grid grid-cols-2 gap-4 mt-4 pt-4 border-t border-gray-100">
                            <div>
                                <label class="block text-[10px] font-bold text-purple-400 uppercase mb-1">Sección</label>
                                <select x-model="commonSeccion"
                                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none text-sm">
                                    <option value="A">Sección A</option>
                                    <option value="B">Sección B</option>
                                    <option value="C">Sección C</option>
                                    <option value="D">Sección D</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-purple-400 uppercase mb-1">Año Lectivo</label>
                                <input type="number" x-model="commonAnio"
                                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none text-sm">
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end">
                            <button type="button" @click="addAssignment()" class="bg-purple-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-purple-700 transition-all flex items-center gap-2">
                                <i class="fas fa-plus"></i> Añadir a la lista
                            </button>
                        </div>
                    </div>

                    <!-- Lista de asignaciones -->
                    <div class="space-y-3">
                        <template x-for="(asig, index) in assignments" :key="index">
                            <div class="bg-white p-4 rounded-xl border border-gray-200 flex justify-between items-center shadow-sm">
                                <div>
                                    <h5 class="text-sm font-bold text-gray-900" x-text="asig.label"></h5>
                                    <p class="text-xs text-gray-500">
                                        Sección <span x-text="asig.seccion"></span> | Año <span x-text="asig.anio_lectivo"></span>
                                        <span x-show="asig.type === 'new'" class="ml-2 px-2 py-0.5 bg-green-100 text-green-700 rounded text-[10px] uppercase font-bold">Nueva</span>
                                    </p>
                                </div>
                                <button type="button" @click="removeAssignment(index)" class="text-gray-400 hover:text-red-500 transition-colors">
                                    <i class="fas fa-trash-alt"></i>
                                </button>

                                <!-- Hidden inputs to submit array -->
                                <input type="hidden" :name="`asignaciones[${index}][type]`" :value="asig.type">
                                <input type="hidden" :name="`asignaciones[${index}][materia_id]`" :value="asig.materia_id">
                                <input type="hidden" :name="`asignaciones[${index}][nombre]`" :value="asig.nombre">
                                <input type="hidden" :name="`asignaciones[${index}][nivel_id]`" :value="asig.nivel_id">
                                <input type="hidden" :name="`asignaciones[${index}][grado_id]`" :value="asig.grado_id">
                                <input type="hidden" :name="`asignaciones[${index}][codigo]`" :value="asig.codigo">
                                <input type="hidden" :name="`asignaciones[${index}][cupo_maximo]`" :value="asig.cupo_maximo">
                                <input type="hidden" :name="`asignaciones[${index}][descripcion]`" :value="asig.descripcion">
                                <input type="hidden" :name="`asignaciones[${index}][seccion]`" :value="asig.seccion">
                                <input type="hidden" :name="`asignaciones[${index}][anio_lectivo]`" :value="asig.anio_lectivo">
                            </div>
                        </template>
                        <div x-show="assignments.length === 0" class="text-center py-4 bg-white/50 rounded-xl border border-dashed border-gray-300">
                            <p class="text-xs text-gray-400">No hay materias asignadas aún. Añada al menos una.</p>
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
        
        // Asignaciones múltiples
        assignments: [],
        assignmentType: 'existing',
        
        // Campos temporales
        selectedMateria: '',
        newMateriaNombre: '',
        newMateriaNivel: '',
        newMateriaGrado: '',
        newMateriaCodigo: '',
        newMateriaCupo: 0,
        newMateriaDesc: '',
        commonSeccion: 'A',
        commonAnio: new Date().getFullYear(),

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

        addAssignment() {
            if (this.assignmentType === 'existing') {
                if (!this.selectedMateria) return alert('Seleccione una materia');
                
                const selectEl = this.$refs.materiaSelect;
                const option = selectEl.options[selectEl.selectedIndex];
                
                this.assignments.push({
                    type: 'existing',
                    materia_id: this.selectedMateria,
                    label: option.getAttribute('data-name'),
                    seccion: this.commonSeccion,
                    anio_lectivo: this.commonAnio,
                    nombre: '', nivel_id: '', grado_id: '', codigo: '', cupo_maximo: '', descripcion: ''
                });
            } else {
                if (!this.newMateriaNombre || !this.newMateriaNivel || !this.newMateriaGrado || !this.newMateriaCodigo) {
                    return alert('Complete todos los campos obligatorios para la nueva materia');
                }
                
                const nivelSelect = this.$refs.nivelSelect;
                const gradoSelect = this.$refs.gradoSelect;
                const nName = nivelSelect.options[nivelSelect.selectedIndex].getAttribute('data-name');
                const gName = gradoSelect.options[gradoSelect.selectedIndex].getAttribute('data-name') || this.grados.find(g=>g.id==this.newMateriaGrado)?.nombre;

                this.assignments.push({
                    type: 'new',
                    materia_id: '',
                    label: `${this.newMateriaNombre} (${nName} - ${gName})`,
                    seccion: this.commonSeccion,
                    anio_lectivo: this.commonAnio,
                    nombre: this.newMateriaNombre,
                    nivel_id: this.newMateriaNivel,
                    grado_id: this.newMateriaGrado,
                    codigo: this.newMateriaCodigo,
                    cupo_maximo: this.newMateriaCupo,
                    descripcion: this.newMateriaDesc
                });
            }
            
            // Reset fields
            this.selectedMateria = '';
            this.newMateriaNombre = '';
            this.newMateriaNivel = '';
            this.newMateriaGrado = '';
            this.newMateriaCodigo = '';
            this.newMateriaCupo = 0;
            this.newMateriaDesc = '';
        },
        
        removeAssignment(index) {
            this.assignments.splice(index, 1);
        },

        next() {
            if (this.isProfessor && this.currentStep === 1) {
                const form = document.getElementById('userForm');
                if (!form.nombre.value || !form.email.value || !form.password.value) {
                    alert('Por favor complete los campos obligatorios.');
                    return;
                }
                document.getElementById('confirm_password_input').value = document.getElementById('password_input').value;
                this.currentStep = 2;
            } else {
                if (this.isProfessor && this.assignments.length === 0) {
                    if (!confirm('No ha asignado ninguna materia. ¿Desea continuar de todos modos?')) {
                        return;
                    }
                }
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
