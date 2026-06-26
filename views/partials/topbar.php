<header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 flex-shrink-0">
    <!-- Search -->
    <div class="relative w-96">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
            <i class="fas fa-search text-sm"></i>
        </span>
        <input type="text" placeholder="Buscar..."
            class="block w-full pl-10 pr-3 py-2 bg-gray-50 border border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none">
    </div>

    <!-- Right Icons -->
    <div class="flex items-center gap-6">
        <div class="relative group">
            <button class="relative text-gray-400 hover:text-gray-600 transition-colors p-2">
                <i class="far fa-bell text-xl"></i>
                <span class="absolute top-1.5 right-1.5 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
            </button>
            <!-- Dropdown Mock -->
            <div class="absolute right-0 mt-2 w-72 bg-white rounded-2xl shadow-xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 p-4">
                <p class="text-xs font-black text-gray-900 uppercase tracking-widest mb-3">Notificaciones</p>
                <div class="space-y-3">
                    <div class="flex gap-3 p-2 hover:bg-gray-50 rounded-xl transition-colors cursor-pointer">
                        <div class="bg-blue-100 text-blue-600 w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-info-circle text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-800">Sincronización completa</p>
                            <p class="text-[10px] text-gray-500">La API se sincronizó correctamente.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative group">
            <button class="text-gray-400 hover:text-gray-600 transition-colors p-2">
                <i class="fas fa-th text-xl"></i>
            </button>
            <div class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 p-4">
                <p class="text-xs font-black text-gray-900 uppercase tracking-widest mb-3">Accesos Rápidos</p>
                <div class="grid grid-cols-2 gap-2">
                    <a href="<?= APP_URL ?>/materias" class="p-3 hover:bg-gray-50 rounded-xl transition-all text-center">
                        <i class="fas fa-book text-blue-600 mb-1"></i>
                        <p class="text-[10px] font-bold text-gray-700">Materias</p>
                    </a>
                    <a href="<?= APP_URL ?>/usuarios" class="p-3 hover:bg-gray-50 rounded-xl transition-all text-center">
                        <i class="fas fa-users text-purple-600 mb-1"></i>
                        <p class="text-[10px] font-bold text-gray-700">Usuarios</p>
                    </a>
                </div>
            </div>
        </div>

        <div class="h-8 w-px bg-gray-200"></div>

        <div class="flex items-center gap-3">
            <div class="text-right hidden sm:block">
                <p class="text-sm font-bold text-gray-900 leading-none"><?= Session::get('user_name') ?></p>
                <p class="text-[10px] text-gray-500 font-semibold uppercase mt-1 tracking-wider"><?= Session::get('role_name') ?></p>
            </div>
            <div class="bg-gray-200 rounded-full h-10 w-10 border-2 border-white shadow-sm overflow-hidden">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode(Session::get('user_name')) ?>&background=random" alt="avatar">
            </div>
        </div>
    </div>
</header>
