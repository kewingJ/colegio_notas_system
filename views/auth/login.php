<form action="<?= APP_URL ?>/auth/doLogin" method="POST" class="space-y-6">
    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= Session::get(CSRF_TOKEN_NAME) ?>">

    <div>
        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Correo Electrónico</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <i class="fas fa-envelope"></i>
            </span>
            <input type="email" id="email" name="email" required
                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                placeholder="admin@arcoiris.edu.ni">
        </div>
    </div>

    <div>
        <div class="flex items-center justify-between mb-2">
            <label for="password" class="block text-sm font-semibold text-gray-700">Contraseña</label>
            <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-500">¿Olvidaste tu contraseña?</a>
        </div>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <i class="fas fa-lock"></i>
            </span>
            <input type="password" id="password" name="password" required
                class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                placeholder="••••••••">
            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                <i class="fas fa-eye" id="eye-icon"></i>
            </button>
        </div>
    </div>

    <button type="submit"
        class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all transform active:scale-95">
        Iniciar Sesión
    </button>
</form>

<script>
function togglePassword() {
    const pass = document.getElementById('password');
    const icon = document.getElementById('eye-icon');
    if (pass.type === 'password') {
        pass.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        pass.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
