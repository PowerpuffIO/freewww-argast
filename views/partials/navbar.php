<?php
declare(strict_types=1);

/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

$u = auth_user();
$isRu = current_lang() === 'ru';
$pref = rtrim((string) ($config['url_prefix'] ?? ''), '/');
$img = fn (string $p) => ($pref === '' ? '' : $pref) . '/themes/argast/' . ltrim($p, '/');
$realmNavRows = argast_realmlist_all();
?>
<header class="pointer-events-auto header-wotlk fixed left-6 right-6 top-6 z-50 transition-all duration-500 rounded-2xl"
        x-data="{ openDropdown: null, mobileMenuOpen: false, arrowLeft: 0,
            setArrowUnder(buttonEl, dropdownEl) {
                if (!buttonEl || !dropdownEl) return;
                const btn = buttonEl.getBoundingClientRect();
                const dd = dropdownEl.getBoundingClientRect();
                this.arrowLeft = Math.max(8, Math.min(dd.width - 8, (btn.left - dd.left) + btn.width / 2));
            }}"
        @click.away="openDropdown = null">
    <div class="px-6 py-3">
        <nav class="flex items-center justify-between">
            <a href="<?= h(url_path('/')) ?>" class="flex items-center space-x-3 hover:opacity-80 transition-opacity duration-300" @click="scrollToTop()">
                <img src="<?= h($img('Images/argast_mini.png')) ?>" alt="ARGAST Logo" class="h-11 w-auto" style="filter: drop-shadow(0 0 3px rgba(255, 255, 255, 0.6)) drop-shadow(0 0 1px rgba(0, 0, 0, 0.8));">
                <div>
                    <span class="text-xl font-beaufort" style="color: #F5E6D3;"><?= h(setting('hero_h1_line1', 'ARGAST')) ?><span class="text-orange-400"><?= h(setting('hero_h1_line2', '.SU')) ?></span></span>
                    <span class="text-xs text-white/70 block leading-none"><?= h(setting('hero_subtitle', '')) ?></span>
                </div>
            </a>
            <div class="hidden xl:flex items-center space-x-8">
                <a href="<?= h(url_path('/news')) ?>" class="nav-link text-white/80 hover:text-white transition-colors text-lg font-medium"><?= h(t('nav_news')) ?></a>
                <a href="<?= h(url_path('/#home')) ?>" class="nav-link text-white/80 hover:text-white transition-colors text-lg font-medium"><?= h(t('nav_start')) ?></a>
                <div class="relative" style="position: relative;">
                    <button type="button" x-ref="statusBtn" @click="openDropdown = openDropdown === 'status' ? null : 'status'; $nextTick(() => { if (openDropdown === 'status') setArrowUnder($refs.statusBtn, $refs.statusDd) })"
                            class="dropdown-btn text-white/80 hover:text-white transition-colors text-lg font-medium flex items-center whitespace-nowrap relative">
                        <span><?= h(t('nav_status')) ?></span>
                        <svg class="w-3 h-3 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                    <div x-cloak x-show="openDropdown === 'status'" x-transition class="absolute top-full left-0 mt-8 min-w-[24rem] w-max max-w-md transition-all duration-500 rounded-xl shadow-2xl p-1 dropdown-wotlk" x-ref="statusDd">
                        <div class="dropdown-arrow dropdown-arrow-wotlk" :style="`left: ${arrowLeft - 8}px`"></div>
                        <?php if (!$realmNavRows): ?>
                        <div class="px-4 py-3 text-gray-400 text-xs"><?= h(t('status_no_realms')) ?></div>
                        <?php else: ?>
                        <?php foreach ($realmNavRows as $rr):
                            $rid = (int) ($rr['id'] ?? 0);
                            $rname = (string) ($rr['name'] ?? '');
                            $gb = (int) ($rr['gamebuild'] ?? 0);
                            $tagWotlk = $gb === 12340;
                            $rOnline = argast_realm_online_players_for_realm($rid);
                            ?>
                        <a href="<?= h(url_path('/status/' . $rid)) ?>" class="block px-4 py-2.5 text-gray-200 hover:text-white hover:bg-white/10 rounded-sm">
                            <div class="text-sm font-medium text-white leading-snug">
                                <?php if ($tagWotlk): ?><span class="text-orange-400 font-semibold uppercase tracking-wide">WOTLK</span> <?php endif; ?><span><?= h($rname) ?></span>
                            </div>
                            <div class="text-xs text-gray-400 leading-snug mt-0.5"><?= h(t('status_nav_online')) ?> <?= h((string) $rOnline) ?></div>
                        </a>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="relative" style="position: relative;">
                    <button type="button" x-ref="communitiesBtn" @click="openDropdown = openDropdown === 'communities' ? null : 'communities'; $nextTick(() => { if (openDropdown === 'communities') setArrowUnder($refs.communitiesBtn, $refs.communitiesDd) })"
                            class="dropdown-btn text-white/80 hover:text-white transition-colors text-lg font-medium flex items-center whitespace-nowrap relative">
                        <span><?= h(t('nav_community')) ?></span>
                        <svg class="w-3 h-3 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                    <div x-cloak x-show="openDropdown === 'communities'" x-transition class="absolute top-full left-0 mt-8 w-40 transition-all duration-500 rounded-xl shadow-2xl p-1 dropdown-wotlk" x-ref="communitiesDd">
                        <div class="dropdown-arrow dropdown-arrow-wotlk" :style="`left: ${arrowLeft - 8}px`"></div>
                        <a href="<?= h(community_url('nav_discord')) ?>" target="_blank" rel="noopener" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-white/10 rounded-sm text-sm"><img src="<?= h($img('Images/Icons/discord.png')) ?>" alt="" class="w-5 h-5 rounded mr-3">Discord</a>
                        <a href="<?= h(community_url('nav_vk')) ?>" target="_blank" rel="noopener" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-white/10 rounded-sm text-sm"><img src="<?= h($img('Images/Icons/vk.png')) ?>" alt="" class="w-5 h-5 rounded mr-3">VK</a>
                        <a href="<?= h(community_url('nav_telegram')) ?>" target="_blank" rel="noopener" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-white/10 rounded-sm text-sm"><img src="<?= h($img('Images/Icons/telegram.png')) ?>" alt="" class="w-5 h-5 rounded mr-3">Telegram</a>
                        <a href="<?= h(community_url('nav_forum')) ?>" target="_blank" rel="noopener" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-white/10 rounded-sm text-sm"><img src="<?= h($img('Images/Icons/forum_icon.png')) ?>" alt="" class="w-5 h-5 rounded mr-3"><?= $isRu ? 'Форум' : 'Forum' ?></a>
                    </div>
                </div>
                <a href="<?= h(community_url('nav_bugtracker')) ?>" target="_blank" rel="noopener" class="nav-link text-white/80 hover:text-white transition-colors text-lg font-medium"><?= h(t('nav_bugtracker')) ?></a>
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative hidden lg:block">
                    <button type="button" x-ref="languageBtn" @click="openDropdown = openDropdown === 'language' ? null : 'language'; $nextTick(() => { if (openDropdown === 'language') setArrowUnder($refs.languageBtn, $refs.languageDd) })"
                            class="flex items-center text-white/80 hover:text-white transition-colors text-lg font-medium whitespace-nowrap">
                        <span><?= $isRu ? h(t('lang_ru')) : h(t('lang_en')) ?></span>
                        <svg class="w-3 h-3 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                    <div x-cloak x-show="openDropdown === 'language'" x-transition class="absolute top-full right-0 mt-8 w-32 transition-all duration-500 rounded-xl shadow-2xl p-1 dropdown-wotlk" x-ref="languageDd">
                        <div class="dropdown-arrow dropdown-arrow-wotlk" :style="`left: ${arrowLeft - 8}px`"></div>
                        <a href="<?= h(lang_switch_url('en')) ?>" class="w-full flex items-center px-3 py-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-sm text-sm <?= !$isRu ? 'text-white bg-white/10' : '' ?>"><img src="<?= h($img('Images/flag_en.png')) ?>" alt="" class="w-4 h-3 rounded-sm mr-2"><?= h(t('lang_en')) ?></a>
                        <a href="<?= h(lang_switch_url('ru')) ?>" class="w-full flex items-center px-3 py-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-sm text-sm <?= $isRu ? 'text-white bg-white/10' : '' ?>"><img src="<?= h($img('Images/flag_ru.png')) ?>" alt="" class="w-4 h-3 rounded-sm mr-2"><?= h(t('lang_ru')) ?></a>
                    </div>
                </div>
                <?php if ($u): ?>
                <div class="hidden md:flex items-center gap-3">
                    <a href="<?= h(url_path('/cabinet')) ?>" class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#3b82f6] hover:bg-[#2563eb] text-white px-5 py-2.5 text-sm font-semibold shadow-lg shadow-blue-900/30 border-2 border-transparent transition-colors"><?= h(t('nav_cabinet')) ?></a>
                    <a href="<?= h(url_path('/logout')) ?>" class="inline-flex items-center justify-center gap-2 rounded-lg border-2 border-white/90 text-white px-5 py-2.5 text-sm font-medium hover:bg-white/10 transition-colors"><?= h(t('nav_logout')) ?></a>
                </div>
                <?php else: ?>
                <div class="hidden md:flex items-center gap-3">
                    <a href="<?= h(url_path('/login')) ?>" class="inline-flex items-center justify-center gap-2 rounded-lg border-2 border-white/90 text-white px-5 py-2.5 text-sm font-medium hover:bg-white/10 transition-colors"><?= h(t('nav_login')) ?></a>
                    <a href="<?= h(url_path('/register')) ?>" class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#3b82f6] hover:bg-[#2563eb] text-white px-5 py-2.5 text-sm font-semibold shadow-lg shadow-blue-900/30 border-2 border-transparent transition-colors"><?= h(t('nav_register')) ?></a>
                </div>
                <?php endif; ?>
                <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="xl:hidden text-white/80 hover:text-white p-2">
                    <svg x-show="!mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </nav>
    </div>
    <div x-cloak x-show="mobileMenuOpen" x-transition class="xl:hidden border-t border-blue-400/20 mt-3">
        <div class="max-h-[calc(100vh-10rem)] overflow-y-auto px-6 pt-4 pb-3">
            <div class="flex flex-col space-y-4">
                <a href="<?= h(url_path('/news')) ?>" class="text-white/80 hover:text-white text-lg py-2" @click="mobileMenuOpen = false"><?= h(t('nav_news')) ?></a>
                <a href="<?= h(url_path('/#home')) ?>" class="text-white/80 hover:text-white text-lg py-2" @click="mobileMenuOpen = false"><?= h(t('nav_start')) ?></a>
                <p class="text-white/50 text-sm pt-2"><?= h(t('nav_status')) ?></p>
                <?php if ($realmNavRows): ?>
                <?php foreach ($realmNavRows as $rr):
                    $mrid = (int) ($rr['id'] ?? 0);
                    $mrname = (string) ($rr['name'] ?? '');
                    $mgb = (int) ($rr['gamebuild'] ?? 0);
                    $mtag = $mgb === 12340;
                    $mOnline = argast_realm_online_players_for_realm($mrid);
                    ?>
                <a href="<?= h(url_path('/status/' . $mrid)) ?>" class="block py-2 pl-2 border-l-2 border-blue-500/40 text-gray-200" @click="mobileMenuOpen = false">
                    <span class="text-sm font-medium leading-snug"><?php if ($mtag): ?><span class="text-orange-400 font-semibold uppercase">WOTLK</span> <?php endif; ?><?= h($mrname) ?></span>
                    <span class="block text-xs text-gray-500 leading-snug mt-0.5"><?= h(t('status_nav_online')) ?> <?= h((string) $mOnline) ?></span>
                </a>
                <?php endforeach; ?>
                <?php else: ?>
                <span class="text-gray-500 text-sm py-1"><?= h(t('status_no_realms')) ?></span>
                <?php endif; ?>
                <a href="<?= h(community_url('nav_discord')) ?>" target="_blank" class="text-white/80 hover:text-white text-lg py-2" @click="mobileMenuOpen = false">Discord</a>
                <a href="<?= h(community_url('nav_vk')) ?>" target="_blank" class="text-white/80 hover:text-white text-lg py-2" @click="mobileMenuOpen = false">VK</a>
                <a href="<?= h(community_url('nav_telegram')) ?>" target="_blank" class="text-white/80 hover:text-white text-lg py-2" @click="mobileMenuOpen = false">Telegram</a>
                <a href="<?= h(lang_switch_url('en')) ?>" class="text-sm py-2"><?= h(t('lang_en')) ?></a>
                <a href="<?= h(lang_switch_url('ru')) ?>" class="text-sm py-2"><?= h(t('lang_ru')) ?></a>
                <?php if ($u): ?>
                <a href="<?= h(url_path('/cabinet')) ?>" class="text-center bg-[#3b82f6] text-white rounded-lg py-3" @click="mobileMenuOpen = false"><?= h(t('nav_cabinet')) ?></a>
                <a href="<?= h(url_path('/logout')) ?>" class="text-center border-2 border-white/90 rounded-lg py-3" @click="mobileMenuOpen = false"><?= h(t('nav_logout')) ?></a>
                <?php else: ?>
                <a href="<?= h(url_path('/login')) ?>" class="text-center border-2 border-white/90 rounded-lg py-3" @click="mobileMenuOpen = false"><?= h(t('nav_login')) ?></a>
                <a href="<?= h(url_path('/register')) ?>" class="text-center bg-[#3b82f6] text-white rounded-lg py-3" @click="mobileMenuOpen = false"><?= h(t('nav_register')) ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
