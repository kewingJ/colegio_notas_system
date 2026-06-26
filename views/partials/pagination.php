<?php
$totalPages = ceil($total / ($perPage ?? 15));
if ($totalPages > 1):
    $currentPage = (int)($page ?? 1);
    $range = 2; // Number of pages to show around current page
?>
<div class="p-6 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between">
    <p class="text-xs text-gray-500">
        Mostrando <span class="font-bold text-gray-900"><?= min($total, (($currentPage - 1) * $perPage) + 1) ?></span>
        a <span class="font-bold text-gray-900"><?= min($total, $currentPage * $perPage) ?></span>
        de <span class="font-bold text-gray-900"><?= $total ?></span> resultados
    </p>

    <div class="flex items-center gap-1">
        <?php if ($currentPage > 1): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage - 1])) ?>"
               class="px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs font-bold text-gray-600 hover:bg-gray-50 transition-all">
                <i class="fas fa-chevron-left mr-1"></i> Anterior
            </a>
        <?php endif; ?>

        <?php
        for ($i = 1; $i <= $totalPages; $i++):
            if ($i == 1 || $i == $totalPages || ($i >= $currentPage - $range && $i <= $currentPage + $range)):
                $isActive = $i === $currentPage;
                $class = $isActive ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50';
        ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"
               class="px-3 py-2 border rounded-lg text-xs font-bold transition-all <?= $class ?>">
                <?= $i ?>
            </a>
        <?php
            elseif (($i == 2 && $currentPage > $range + 2) || ($i == $totalPages - 1 && $currentPage < $totalPages - $range - 1)):
        ?>
            <span class="px-2 text-gray-400 text-xs">...</span>
        <?php
                $i = ($i == 2) ? $currentPage - $range - 1 : $totalPages - 1;
            endif;
        endfor;
        ?>

        <?php if ($currentPage < $totalPages): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage + 1])) ?>"
               class="px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs font-bold text-gray-600 hover:bg-gray-50 transition-all">
                Siguiente <i class="fas fa-chevron-right ml-1"></i>
            </a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
