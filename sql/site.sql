-- This website is provided free of charge.
-- Author: Powerpuff — https://powerpuff.pro/
-- Discord: https://discord.gg/QwCsWtP99A
-- GitHub: https://github.com/PowerpuffIO

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `game_account_id` int unsigned NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `game_account_id` (`game_account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `news` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(191) NOT NULL,
  `title_ru` varchar(500) NOT NULL,
  `title_en` varchar(500) NOT NULL,
  `excerpt_ru` text,
  `excerpt_en` text,
  `body_ru` mediumtext,
  `body_en` mediumtext,
  `image_path` varchar(512) NOT NULL DEFAULT '',
  `published_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `published_at` (`published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `site_settings` (
  `skey` varchar(128) NOT NULL,
  `value_ru` mediumtext,
  `value_en` mediumtext,
  PRIMARY KEY (`skey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `community_links` (
  `link_key` varchar(64) NOT NULL,
  `url_ru` varchar(1024) NOT NULL DEFAULT '',
  `url_en` varchar(1024) NOT NULL DEFAULT '',
  PRIMARY KEY (`link_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `videos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `youtube_id` varchar(32) NOT NULL,
  `title_ru` varchar(500) NOT NULL,
  `title_en` varchar(500) NOT NULL,
  `sort_order` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `youtube_id` (`youtube_id`),
  KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `static_pages` (
  `slug` varchar(64) NOT NULL,
  `body_ru` mediumtext,
  `body_en` mediumtext,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `votes_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `game_account_id` int unsigned NOT NULL,
  `reward_day` date NOT NULL COMMENT 'UTC calendar day used for one reward per day',
  `character_name` varchar(64) DEFAULT NULL,
  `bonus_amount` int NOT NULL,
  `mmorating_checked_at` varchar(64) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_reward_day` (`user_id`,`reward_day`),
  KEY `idx_game_account` (`game_account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `site_settings` (`skey`, `value_ru`, `value_en`) VALUES
('meta_title', 'Argast.su — бесплатные сервера WoW | Argast.su', 'Argast.su — Free WoW Servers | Argast.su'),
('hero_h1_line1', 'ARGAST', 'ARGAST'),
('hero_h1_line2', '.SU', '.SU'),
('hero_subtitle', 'комплекс игровых серверов', 'game server community'),
('hero_taglines', '["Комплекс серверов World of Warcraft","Бесплатная игра без компромиссов","Эпические приключения в классическом Азероте","Стабильные серверы с активным сообществом","Ваши самые любимые игровые дополнения"]', '["World of Warcraft server community","Free to play without compromise","Epic adventures in classic Azeroth","Stable servers with an active community","Your favorite game expansions"]'),
('news_section_title', 'Последние новости', 'Latest news'),
('news_section_sub', 'Будьте в курсе всех событий на серверах ARGAST', 'Stay up to date with ARGAST'),
('news_all_btn', 'Все новости', 'All news'),
('video_section_title', 'Наши видео', 'Our videos'),
('video_section_sub', 'Трейлеры, обзоры, геймплей и подкасты о серверах', 'Trailers, reviews, gameplay and podcasts'),
('community_title', 'Присоединяйтесь к сообществу!', 'Join the community!'),
('community_sub', '', '')
ON DUPLICATE KEY UPDATE `skey` = VALUES(`skey`);

INSERT INTO `community_links` (`link_key`, `url_ru`, `url_en`) VALUES
('nav_discord', 'https://discord.gg/QwCsWtP99A', 'https://discord.gg/QwCsWtP99A'),
('nav_vk', 'https://powerpuff.pro/', 'https://powerpuff.pro/'),
('nav_telegram', 'https://powerpuff.pro/', 'https://powerpuff.pro/'),
('nav_forum', 'https://powerpuff.pro/', 'https://powerpuff.pro/'),
('nav_bugtracker', 'https://powerpuff.pro/', 'https://powerpuff.pro/'),
('section_discord', 'https://discord.gg/QwCsWtP99A', 'https://discord.gg/QwCsWtP99A'),
('section_vk', 'https://powerpuff.pro/', 'https://powerpuff.pro/'),
('section_telegram', 'https://powerpuff.pro/', 'https://powerpuff.pro/')
ON DUPLICATE KEY UPDATE `link_key` = VALUES(`link_key`);

INSERT INTO `static_pages` (`slug`, `body_ru`, `body_en`, `updated_at`) VALUES
('privacy', '<p>Политика конфиденциальности.</p>', '<p>Privacy policy.</p>', NOW()),
('terms', '<p>Пользовательское соглашение.</p>', '<p>Terms of service.</p>', NOW()),
('refund', '<p>Политика возврата.</p>', '<p>Refund policy.</p>', NOW())
ON DUPLICATE KEY UPDATE `slug` = VALUES(`slug`);

INSERT IGNORE INTO `videos` (`youtube_id`, `title_ru`, `title_en`, `sort_order`) VALUES
('d8wfHmKvPoo', 'Официальное видео проекта', 'Official project video', 0),
('SlMD8BJydtY', 'Старое промо', 'Classic promo', 1),
('ymKGwDUwrSM', 'Обзор на режим Mythic+ в WotLK', 'Mythic+ mode overview for WotLK', 2);

SET FOREIGN_KEY_CHECKS = 1;
