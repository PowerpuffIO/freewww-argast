<!--
  This website is provided free of charge.
  Author: Powerpuff — https://powerpuff.pro/
  Discord: https://discord.gg/QwCsWtP99A
  GitHub: https://github.com/PowerpuffIO
-->

<p align="center">
  <img src="./themes/powerpuff/en.png" alt="English" width="360" />
  &nbsp;&nbsp;
  <img src="./themes/powerpuff/ru.png" alt="Russian" width="360" />
</p>

# Private WoW server website — installation

This README assumes the **contents of this folder** are deployed as your web root (no extra parent folder in URLs or paths).

## Attribution

**This website is provided free of charge.**

| | |
|--|--|
| **Author** | Powerpuff |
| **Website** | [powerpuff.pro](https://powerpuff.pro/) |
| **Discord** | [discord.gg/QwCsWtP99A](https://discord.gg/QwCsWtP99A) |
| **GitHub** | [github.com/PowerpuffIO](https://github.com/PowerpuffIO) |

## Requirements

- **PHP** 8.1+ with extensions: `pdo_mysql`, `json`, `session`, `mbstring` (and `curl` recommended for the vote API).
- **MySQL / MariaDB** — TrinityCore / AzerothCore style: `auth`, `characters`, plus a **site** database for CMS data.
- **Web server** — Apache with `mod_rewrite` (see `.htaccess`) or nginx with equivalent rewrite to `index.php`.

## 1. Deployment

Upload this folder’s contents to your vhost document root, or point the site root here.

## 2. Configuration

Edit `config.php`:

| Setting | Purpose |
|--------|---------|
| `url_prefix` | URL path if the app is not at domain root (e.g. `/argast`). |
| `base_path` | Same for generated links (often empty at root). |
| `site_db` | Database for news, settings, users, vote log, etc. |
| `auth_db` / `characters_db` | Game `auth` and `characters` databases. |
| `upload_dir` | Writable path for uploads (default: `storage/uploads` under this folder). |

## 3. Database

1. Create an empty database for the site (e.g. `argast_site`).
2. Import SQL from `sql/` in a sensible order, for example:
   - `sql/site.sql`
   - `sql/auth_account_donate.sql` into the **auth** database (if you use donate bonuses)
3. Promote a user to admin: see `sql/grant_admin.sql` (adjust user id as needed).

Ensure the game `realmlist` table lists your realms; the **Status** menu reads from it.

## 4. Permissions

Make `storage/uploads` (and `storage/uploads/news` if used) writable by the web server user.

## 5. First steps

- Register at `/register.php` or use the admin entry point (`powerpuffsiteadmin.php` + `section=`).
- Configure texts, news, and optional MMORating voting in the admin panel.

## Screenshots

<p align="center">
  <a href="./themes/powerpuff/SCREENSHOTS.md"><strong>View screenshots</strong></a>
</p>

Place extra images in `themes/powerpuff/`, then regenerate the gallery (from **this folder** in a shell):

```bash
php themes/powerpuff/build-screenshots-md.php
```

### Language preview images

Add **`en.png`** and **`ru.png`** under `themes/powerpuff/` so the images at the top of this README resolve correctly (e.g. on GitHub).
