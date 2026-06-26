<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?> | <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        // Preservar posición del scroll al recargar o navegar
        document.addEventListener("DOMContentLoaded", function() {
            const scrollPos = localStorage.getItem("scrollPos");
            if (scrollPos) {
                window.scrollTo(0, parseInt(scrollPos));
                localStorage.removeItem("scrollPos");
            }
        });

        window.onbeforeunload = function() {
            localStorage.setItem("scrollPos", window.scrollY);
        };
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .sidebar-link.active { background-color: #eff6ff; color: #2563eb; border-right: 4px solid #2563eb; }
        .sidebar-link:hover:not(.active) { background-color: #f1f5f9; }
    </style>
</head>
<body class="flex min-h-screen">

    <!-- Sidebar -->
    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col min-w-0">

        <!-- Topbar -->
        <?php require_once __DIR__ . '/../partials/topbar.php'; ?>

        <!-- Alerts -->
        <div class="px-8 mt-4">
            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>
        </div>

        <!-- API Connection Alert -->
        <?php if (isset($apiOffline) && $apiOffline): ?>
            <div class="mx-8 mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            No se pudo conectar con el sistema principal. Mostrando datos desde el caché local.
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="px-8 py-6">
            <h1 class="text-2xl font-bold text-gray-800"><?= $title ?></h1>
            <?php if (isset($subtitle)): ?>
                <p class="text-gray-500 text-sm mt-1"><?= $subtitle ?></p>
            <?php endif; ?>
        </div>

        <!-- Content Area -->
        <div class="px-8 pb-8">
            <?= $content ?>
        </div>

    </main>

    <!-- Modals Container -->
    <div id="modal-container"></div>

    <script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>
</html>
