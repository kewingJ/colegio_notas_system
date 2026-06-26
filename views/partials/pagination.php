<?php
$totalPages = ceil($total / $perPage);
if ($totalPages > 1): ?>
<div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
    <p class="text-xs text-gray-500">
        Mostrando <span class="font-bold text-gray-900"><?= min($total, ($page - 1) * $perPage + 1) ?></span> a
        <span class="font-bold text-gray-900"><?= min($total, $page * $perPage) ?></span> de
        <span class="font-bold text-gray-900"><?= $total ?></span> registros
    </p>

    <div class="flex items-center gap-1">
        <?php
        $queryParams = $_GET;
        function getPageUrl($p, $params) {
            $params['page'] = $p;
            return APP_URL . '/' . ($_GET['url'] ?? '') . '?' . http_build_query($params);
        }
        ?>

        <?php if ($page > 1): ?>
            <a href="<?= getPageUrl($page - 1, $queryParams) ?>" class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-bold text-gray-600 hover:bg-white transition-all">Anterior</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="<?= getPageUrl($i, $queryParams) ?>" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all <?= $i == $page ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'text-gray-600 hover:bg-white border border-transparent hover:border-gray-200' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="<?= getPageUrl($page + 1, $queryParams) ?>" class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-bold text-gray-600 hover:bg-white transition-all">Siguiente</a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
