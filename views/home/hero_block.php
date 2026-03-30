<?php
declare(strict_types=1);

/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

$lang = current_lang();
$pref = rtrim((string) ($config['url_prefix'] ?? ''), '/');
$vsrc = $pref . '/themes/argast/Video/wotlk_background.webm';
$div = $pref . '/themes/argast/Images/Backgrounds/divider_up.svg';
?>
<main class="flex-grow">
    <section id="home" class="relative min-h-screen flex items-center justify-center overflow-hidden pt-20">
        <div id="wotlk-video-container" class="pointer-events-none absolute inset-0 z-10 w-full h-full opacity-100">
            <video id="wotlkVideo" class="absolute inset-0 w-full h-full object-cover video-background" autoplay muted loop playsinline preload="auto" src="<?= h($vsrc) ?>"></video>
            <div class="absolute inset-0 bg-black/50"></div>
            <div class="absolute top-0 left-0 right-0 h-32 bg-gradient-to-b from-black/80 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 h-44 bg-gradient-to-t fade-blur" :style="getVideoGradientStyle()"></div>
        </div>
        <div class="relative z-10 pointer-events-auto text-center text-white px-6 max-w-7xl mx-auto">
            <h1 class="text-5xl lg:text-7xl font-beaufort mb-4 text-shadow" style="color: #F5E6D3;">
                <?= h(setting('hero_h1_line1', 'ARGAST')) ?><span class="text-orange-400"><?= h(setting('hero_h1_line2', '.SU')) ?></span>
            </h1>
            <div class="text-xl lg:text-2xl mb-8 text-shadow font-medium opacity-90 min-h-8 flex items-center justify-center relative w-full"
                 x-data="{
                     taglines: (typeof window.__HERO_TAGLINES__ !== 'undefined' && window.__HERO_TAGLINES__.length) ? window.__HERO_TAGLINES__ : ['<?= $lang === 'en' ? 'World of Warcraft' : 'Комплекс серверов World of Warcraft' ?>'],
                     currentIndex: 0,
                     isChanging: false,
                     changeTagline() {
                         this.isChanging = true;
                         setTimeout(() => {
                             this.currentIndex = (this.currentIndex + 1) % this.taglines.length;
                             this.isChanging = false;
                         }, 400);
                     }
                 }"
                 x-init="setInterval(() => { changeTagline() }, 7000)">
                <p class="transition-all duration-400 ease-in-out transform text-center w-full px-2"
                   :class="isChanging ? 'opacity-0 translate-x-8' : 'opacity-100 translate-x-0'"
                   x-text="taglines[currentIndex]"></p>
            </div>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="<?= h(url_path('/register')) ?>" class="font-beaufort inline-flex items-center justify-center px-8 py-3 rounded-md text-lg font-semibold text-white bg-[#3b82f6] hover:bg-[#2563eb] shadow-xl shadow-blue-900/35 border-0 transition-all duration-300 hover:scale-[1.02]"><?= h(t('hero_play')) ?></a>
                <button type="button" @click="scrollToVideo()" class="border-2 border-orange-400 text-orange-400 hover:bg-orange-400 hover:text-black font-beaufort px-8 py-3 rounded-md transition-all duration-300 transform hover:scale-105 text-lg"><?= h(t('hero_promo')) ?></button>
            </div>
        </div>
        <div class="absolute left-0 right-0 w-full z-20" style="bottom: -6px; margin: 0; padding: 0; line-height: 0; font-size: 0;">
            <img src="<?= h($div) ?>" alt="" class="w-full h-auto" :class="getDividerClass()" style="display: block; margin: 0; padding: 0; vertical-align: bottom;">
        </div>
    </section>
