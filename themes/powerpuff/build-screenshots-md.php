<?php

declare(strict_types=1);

/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 *
 * Regenerates SCREENSHOTS.md from image files in this directory.
 * Usage (from site root, the folder that contains index.php): php themes/powerpuff/build-screenshots-md.php
 */
$dir = __DIR__;
$exclude = basename(__FILE__);
$skip = ['en.png', 'ru.png', 'SCREENSHOTS.md', $exclude, '.gitkeep'];
$paths = [];
foreach (['png', 'jpg', 'jpeg', 'webp', 'gif'] as $e) {
    foreach (glob($dir . '/*.' . $e) ?: [] as $p) {
        $paths[] = $p;
    }
}
$files = [];
foreach ($paths as $path) {
    $b = basename($path);
    if (in_array($b, $skip, true)) {
        continue;
    }
    $files[] = $b;
}
natcasesort($files);
$files = array_values($files);

$md = "# Installation screenshots\n\n";
$md .= "Extra images from this folder (language previews **en.png** and **ru.png** are in the [main README](../../README.md)).\n\n";
$md .= "---\n\n";
if ($files === []) {
    $md .= "*No extra screenshots yet. Add `.png` / `.jpg` files here and run `php themes/powerpuff/build-screenshots-md.php` from the site root (folder with `index.php`) to update this file.*\n";
} else {
    foreach ($files as $b) {
        $md .= "## `{$b}`\n\n![{$b}]({$b})\n\n";
    }
}

file_put_contents($dir . '/SCREENSHOTS.md', $md);
fwrite(STDOUT, 'SCREENSHOTS.md written (' . count($files) . " images).\n");
