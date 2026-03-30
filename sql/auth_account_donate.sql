-- This website is provided free of charge.
-- Author: Powerpuff — https://powerpuff.pro/
-- Discord: https://discord.gg/QwCsWtP99A
-- GitHub: https://github.com/PowerpuffIO

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `account_donate`;
CREATE TABLE `account_donate` (
  `id` int NOT NULL,
  `bonuses` int NOT NULL DEFAULT 0,
  `votes` int NOT NULL DEFAULT 0,
  `total_votes` int NOT NULL DEFAULT 0,
  `total_bonuses` int NOT NULL DEFAULT 0,
  `banned` int DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

SET FOREIGN_KEY_CHECKS = 1;
