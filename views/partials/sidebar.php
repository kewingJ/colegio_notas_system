<aside class="w-64 bg-white border-r border-gray-200 flex flex-col flex-shrink-0">
    <div class="p-6">
        <div class="flex items-center gap-3 mb-8">
            <div class="bg-blue-600 w-10 h-10 rounded-xl flex items-center justify-center shadow-lg shadow-blue-100">
                <i class="fas fa-graduation-cap text-white"></i>
            </div>
            <div>
                <h2 class="font-bold text-gray-900 leading-tight">Arcoíris</h2>
                <p class="text-xs text-gray-500">Gestión Académica</p>
            </div>
        </div>

        <nav class="space-y-1">
            <?php
            $currentUrl = $_GET['url'] ?? '';
            function navItem($url, $icon, $label, $currentUrl) {
                $isActive = (strpos($currentUrl, $url) === 0) || ($url === 'dashboard' && empty($currentUrl));
                $activeClass = $isActive ? 'active' : '';
                $href = APP_URL . '/' . $url;
                return "<a href='$href' class='sidebar-link $activeClass flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-gray-600 transition-all'>
                            <i class='fas fa-$icon w-5 text-center'></i>
                            <span>$label</span>
                        </a>";
            }
            ?>

            <?= navItem('dashboard', 'th-large', 'Panel Principal', $currentUrl) ?>

            <div class="pt-4 pb-2 px-4">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Administración</p>
            </div>

            <?= navItem('usuarios', 'users', 'Gestión de Usuarios', $currentUrl) ?>
            <?= navItem('materias', 'book', 'Gestión de Materias', $currentUrl) ?>
            <?= navItem('horarios', 'calendar-alt', 'Horarios', $currentUrl) ?>

            <div class="pt-4 pb-2 px-4">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Académico</p>
            </div>

            <?= navItem('calificaciones', 'star', 'Calificaciones', $currentUrl) ?>
            <?= navItem('estudiantes', 'user-graduate', 'Estudiantes', $currentUrl) ?>
        </nav>
    </div>

    <div class="mt-auto p-4 border-t border-gray-100">
        <div class="bg-gray-50 rounded-xl p-4 mb-4">
            <div class="flex items-center gap-3">
                <div class="bg-blue-100 text-blue-600 w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs">
                    <?= substr(Session::get('user_name'), 0, 1) ?>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-gray-900 truncate"><?= Session::get('user_name') ?></p>
                    <p class="text-[10px] text-gray-500 uppercase font-semibold"><?= Session::get('role_name') ?></p>
                </div>
            </div>
        </div>

        <a href="<?= APP_URL ?>/auth/logout" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition-all">
            <i class="fas fa-sign-out-alt w-5 text-center"></i>
            <span>Cerrar Sesión</span>
        </a>
    </div>
</aside>
