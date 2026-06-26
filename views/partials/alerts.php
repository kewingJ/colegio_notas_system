<?php
$flashes = Session::getFlash();
if (!empty($flashes)): ?>
<div id="alert-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3">
    <?php foreach ($flashes as $type => $message):
        $bgColor = 'bg-blue-500';
        $icon = 'info-circle';
        if ($type === 'error') { $bgColor = 'bg-red-500'; $icon = 'exclamation-circle'; }
        if ($type === 'success') { $bgColor = 'bg-green-500'; $icon = 'check-circle'; }
        if ($type === 'warning') { $bgColor = 'bg-yellow-500'; $icon = 'exclamation-triangle'; }
    ?>
    <div class="alert-item <?= $bgColor ?> text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 transform transition-all duration-300 animate-slide-in">
        <i class="fas fa-<?= $icon ?>"></i>
        <span class="font-medium"><?= h($message) ?></span>
        <button onclick="this.parentElement.remove()" class="ml-4 hover:text-gray-200">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <?php endforeach; ?>
</div>

<style>
@keyframes slide-in {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
.animate-slide-in { animation: slide-in 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
</style>

<script>
setTimeout(() => {
    document.querySelectorAll('.alert-item').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateX(20px)';
        setTimeout(() => el.remove(), 300);
    });
}, 4000);
</script>
<?php endif; ?>
