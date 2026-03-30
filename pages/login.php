<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

$pageTitle = t('login_title') . ' — ' . setting('meta_title');
require dirname(__DIR__) . '/views/layout/auth_page_start.php';
?>
<div class="w-full max-w-md">
    <div class="bg-blue-950/40 border border-blue-500/30 rounded-2xl p-8 shadow-2xl backdrop-blur-md">
        <h1 class="text-2xl font-beaufort mb-6 text-center" style="color: #F5E6D3;"><?= h(t('login_title')) ?></h1>
        <form id="logf" class="space-y-4">
            <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
            <div>
                <label class="block text-sm text-gray-300 mb-1"><?= h(t('login_username')) ?></label>
                <input type="text" name="username" required class="w-full rounded-lg bg-black/40 border border-white/20 px-4 py-2 text-white" autocomplete="username">
            </div>
            <div>
                <label class="block text-sm text-gray-300 mb-1"><?= h(t('login_password')) ?></label>
                <input type="password" name="password" required class="w-full rounded-lg bg-black/40 border border-white/20 px-4 py-2 text-white" autocomplete="current-password">
            </div>
            <p class="text-right text-sm"><a href="<?= h(url_path('/forgot-password')) ?>" class="text-blue-400 hover:underline"><?= h(t('login_forgot_password')) ?></a></p>
            <p id="logmsg" class="text-sm text-red-400 hidden"></p>
            <button type="submit" class="w-full font-beaufort py-3 rounded-lg bg-[#3b82f6] hover:bg-[#2563eb] text-white font-semibold"><?= h(t('login_submit')) ?></button>
        </form>
        <p class="text-center text-gray-400 mt-6 text-sm"><?= h(t('login_no_account')) ?> <a href="<?= h(url_path('/register')) ?>" class="text-blue-400 hover:underline"><?= h(t('login_register')) ?></a></p>
    </div>
</div>
<script>
document.getElementById('logf').addEventListener('submit', async function(e) {
    e.preventDefault();
    const msg = document.getElementById('logmsg');
    msg.classList.add('hidden');
    const fd = new FormData(this);
    const r = await fetch('<?= h(url_path('/login')) ?>', { method: 'POST', body: fd });
    const j = await r.json();
    if (j.ok) { window.location.href = j.redirect; return; }
    msg.textContent = j.message || 'Error';
    msg.classList.remove('hidden');
});
</script>
<?php require dirname(__DIR__) . '/views/layout/auth_page_end.php';
