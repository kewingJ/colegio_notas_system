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
        <button class="relative text-gray-400 hover:text-gray-600 transition-colors">
            <i class="far fa-bell text-xl"></i>
            <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
        </button>

        <button class="text-gray-400 hover:text-gray-600 transition-colors">
            <i class="fas fa-th text-xl"></i>
        </button>

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
