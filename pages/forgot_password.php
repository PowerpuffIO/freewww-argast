<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

$pageTitle = t('forgot_password_title') . ' — ' . setting('meta_title');
require dirname(__DIR__) . '/views/layout/auth_page_start.php';
?>
<div class="w-full max-w-md">
    <div class="bg-blue-950/40 border border-blue-500/30 rounded-2xl p-8 shadow-2xl backdrop-blur-md">
        <h1 class="text-2xl font-beaufort mb-6 text-center" style="color: #F5E6D3;"><?= h(t('forgot_password_title')) ?></h1>
        <p class="text-gray-300 text-sm text-center leading-relaxed mb-4"><?= h(t('forgot_password_notice')) ?></p>
        <form id="forgotf" class="space-y-4">
            <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
            <?php require dirname(__DIR__) . '/views/partials/auth_captcha.php'; ?>
            <p id="forgotmsg" class="text-sm hidden"></p>
            <button type="submit" class="w-full font-beaufort py-3 rounded-lg bg-[#3b82f6] hover:bg-[#2563eb] text-white font-semibold"><?= h(t('forgot_password_submit')) ?></button>
        </form>
        <p class="text-center text-gray-400 text-sm mt-4"><a href="<?= h(url_path('/login')) ?>" class="text-blue-400 hover:underline"><?= h(t('forgot_password_back_login')) ?></a></p>
    </div>
</div>
<script>
document.getElementById('forgotf').addEventListener('submit', async function(e) {
    e.preventDefault();
    const msg = document.getElementById('forgotmsg');
    msg.classList.add('hidden');
    const fd = new FormData(this);
    const r = await fetch('<?= h(url_path('/forgot-password')) ?>', { method: 'POST', body: fd });
    const j = await r.json();
    msg.textContent = j.message || '';
    msg.className = 'text-sm ' + (j.ok ? 'text-blue-300' : 'text-red-400');
    msg.classList.remove('hidden');
    if (!j.ok) {
        if (window.grecaptcha) { try { grecaptcha.reset(); } catch (e) {} }
        if (window.turnstile) { document.querySelectorAll('.cf-turnstile').forEach(function(el) { try { turnstile.reset(el); } catch (e) {} }); }
    }
});
</script>
<?php require dirname(__DIR__) . '/views/layout/auth_page_end.php';
