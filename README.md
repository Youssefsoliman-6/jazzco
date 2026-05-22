# 🎵 JazzCo — PHP/MySQL Music Streaming Platform

> A modern, dark-themed music streaming web app built with PHP, MySQL, and Vanilla JavaScript — featuring a global audio player, admin dashboard, and full user account system.

---

## ✨ Features

### For Users
- 🎧 **Global bottom music player** — persists across page navigation with queue, shuffle, repeat, volume, and progress bar
- 🔍 **AJAX live search** — instant results for songs, artists, albums, and playlists
- 📂 **Library & playlist management** — create, edit, delete playlists; add/remove songs; toggle privacy
- ❤️ **Favorites & recently played** — personalized listening history
- 💿 **Albums** — browse, create, and manage albums with cover art
- 👤 **User profiles** — avatar upload, account settings, password management
- 🌗 **Dark / Light mode** — theme toggle saved in `localStorage`
- ⌨️ **Keyboard shortcuts** — control playback without touching the mouse

### For Admins
- 📊 **Dashboard with statistics**
- 🎵 **Full CRUD** for songs, artists, albums, genres, playlists, and users
- 📁 **MP3 and cover image uploads** with validation
- 🛡️ **Secure admin login** — separate from user authentication

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.2 |
| Database | MySQL / MariaDB (via phpMyAdmin) |
| Frontend | HTML5, CSS3, Vanilla JavaScript |
| Server | Apache (XAMPP) |
| Auth | PHP sessions + bcrypt password hashing |

---

## 🚀 Getting Started

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (or any Apache + MySQL + PHP 8+ stack)

### Installation

1. **Clone or download** this repository into your XAMPP `htdocs` folder:
   ```
   C:\xampp\htdocs\jazzco          ← Windows
   /Applications/XAMPP/htdocs/jazzco  ← macOS
   /opt/lampp/htdocs/jazzco           ← Linux
   ```

2. **Start Apache and MySQL** from the XAMPP Control Panel.

3. **Import the database:**
   - Open `http://localhost/phpmyadmin`
   - Click **Import** → select `jazzco_db.sql` → click **Go**

4. **Visit the app:**
   - Website: `http://localhost/jazzco/`
   - Player: `http://localhost/jazzco/player.php`
   - Admin: `http://localhost/jazzco/admin/login.php`

### Default Credentials

| Role | URL | Username / Email | Password |
|---|---|---|---|
| Admin | `/admin/login.php` | `admin` | `admin123` |
| Demo User | `/login.php` | `demo@jazzco.local` | `user123` |

> ⚠️ Change these passwords after your first login.

---

## ⚙️ Configuration

Database connection is set in `includes/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'jazzco_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

Edit these if your MySQL credentials differ from the XAMPP defaults.

---

## 📁 Project Structure

```
/jazzco
├── /admin              ← Admin dashboard pages (songs, artists, albums, users...)
├── /assets
│   ├── /css            ← Main stylesheet
│   ├── /js             ← Player logic and app scripts
│   ├── /images         ← Default covers and avatars (SVG)
│   └── /songs          ← Demo MP3 files
├── /includes           ← Config, DB connection, header/footer, helper functions
├── /uploads            ← User-uploaded songs, covers, artist photos, avatars
├── index.php           ← Landing page
├── player.php          ← Full-screen player view
├── library.php         ← Song library
├── albums.php          ← Albums browser
├── album.php           ← Individual album page
├── playlists.php       ← Playlists browser
├── playlist.php        ← Individual playlist page
├── profile.php         ← User profile & settings
├── login.php
├── register.php
├── search.php
├── jazzco_db.sql       ← Full database schema + seed data
└── upgrade_add_user_albums.sql  ← Run this if upgrading from an older version
```

---

## 🔌 API Endpoints

| Endpoint | Description |
|---|---|
| `api_song.php` | Single song data |
| `api_songs.php` | Song list |
| `playlist_api.php` | Playlist CRUD (create, delete, add/remove songs, toggle privacy) |
| `favorite_api.php` | Toggle song favorites |
| `recent_api.php` | Recently played tracking |

---

## 🐛 Troubleshooting

### Uploads failing on macOS
Run the diagnostic page first:
```
http://localhost/jazzco/setup_check.php
```
If any upload folder shows **Fix needed**, run:
```bash
chmod -R 777 /Applications/XAMPP/xamppfiles/htdocs/jazzco/uploads
```
Then restart Apache from XAMPP Manager.

### Large MP3 files timing out
Edit `/Applications/XAMPP/xamppfiles/etc/php.ini` (macOS) or `C:\xampp\php\php.ini` (Windows):
```ini
file_uploads = On
upload_max_filesize = 100M
post_max_size = 120M
max_execution_time = 300
memory_limit = 256M
```
Restart Apache after saving.

### Upgrading from an older JazzCo database
If you already have an existing `jazzco_db` imported, run only the upgrade script — do **not** re-import the full `jazzco_db.sql`:
```
http://localhost/phpmyadmin → Import → upgrade_add_user_albums.sql
```

---

## 📸 Screenshots

> _Add screenshots of the landing page, player, and admin dashboard here._

---

## 👥 Team

Developed as a software engineering project at **Misr International University (MIU)**.

- Eng. Ahmed Yasser
- Eng. Janna Sherif
- Eng. Youssef Ahmed

---

## 📄 License

This project was developed for academic purposes. All rights reserved by the project authors.
