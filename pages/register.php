<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

$pageTitle = t('register_title') . ' — ' . setting('meta_title');
require dirname(__DIR__) . '/views/layout/auth_page_start.php';
?>
<div class="w-full max-w-md">
    <div class="bg-blue-950/40 border border-blue-500/30 rounded-2xl p-8 shadow-2xl backdrop-blur-md">
        <h1 class="text-2xl font-beaufort mb-6 text-center" style="color: #F5E6D3;"><?= h(t('register_title')) ?></h1>
        <form id="regf" class="space-y-4">
            <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
            <div>
                <label class="block text-sm text-gray-300 mb-1"><?= h(t('register_username')) ?></label>
                <input type="text" name="username" required maxlength="14" pattern="[A-Za-z0-9]+" class="w-full rounded-lg bg-black/40 border border-white/20 px-4 py-2 text-white" autocomplete="username">
                <p class="text-xs text-gray-500 mt-1"><?= h(t('register_username_hint')) ?></p>
            </div>
            <div>
                <label class="block text-sm text-gray-300 mb-1"><?= h(t('register_email')) ?></label>
                <input type="email" name="email" required class="w-full rounded-lg bg-black/40 border border-white/20 px-4 py-2 text-white" autocomplete="email">
            </div>
            <div>
                <label class="block text-sm text-gray-300 mb-1"><?= h(t('register_password')) ?></label>
                <input type="password" name="password" required minlength="8" class="w-full rounded-lg bg-black/40 border border-white/20 px-4 py-2 text-white" autocomplete="new-password">
            </div>
            <div>
                <label class="block text-sm text-gray-300 mb-1"><?= h(t('register_password_confirm')) ?></label>
                <input type="password" name="password_confirm" required minlength="8" class="w-full rounded-lg bg-black/40 border border-white/20 px-4 py-2 text-white" autocomplete="new-password">
            </div>
            <?php require dirname(__DIR__) . '/views/partials/auth_captcha.php'; ?>
            <p id="regmsg" class="text-sm text-red-400 hidden"></p>
            <button type="submit" class="w-full font-beaufort py-3 rounded-lg bg-[#3b82f6] hover:bg-[#2563eb] text-white font-semibold"><?= h(t('register_submit')) ?></button>
        </form>
        <p class="text-center text-gray-400 mt-6 text-sm"><?= h(t('register_have_account')) ?> <a href="<?= h(url_path('/login')) ?>" class="text-blue-400 hover:underline"><?= h(t('register_signin')) ?></a></p>
    </div>
</div>
<script>
document.getElementById('regf').addEventListener('submit', async function(e) {
    e.preventDefault();
    const msg = document.getElementById('regmsg');
    msg.classList.add('hidden');
    const fd = new FormData(this);
    const r = await fetch('<?= h(url_path('/register')) ?>', { method: 'POST', body: fd });
    const j = await r.json();
    if (j.ok) { window.location.href = j.redirect; return; }
    msg.textContent = j.message || 'Error';
    msg.classList.remove('hidden');
    if (window.grecaptcha) { try { grecaptcha.reset(); } catch (e) {} }
    if (window.turnstile) { document.querySelectorAll('.cf-turnstile').forEach(function(el) { try { turnstile.reset(el); } catch (e) {} }); }
});
</script>
<?php require dirname(__DIR__) . '/views/layout/auth_page_end.php';
