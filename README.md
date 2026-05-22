# JazzCO — PHP/MySQL Music Streaming Platform

JazzCO is a modern dark-themed music streaming website built with HTML5, CSS3, Vanilla JavaScript, PHP, MySQL, and XAMPP/Apache.

## Default logins

Admin dashboard:
- URL: `http://localhost/jazzco/admin/login.php`
- Username: `admin`
- Password: `admin123`

Demo user:
- URL: `http://localhost/jazzco/login.php`
- Email: `demo@jazzco.local`
- Password: `user123`

Change these passwords after importing the database.

## XAMPP setup

1. Install XAMPP.
2. Start **Apache** and **MySQL** from the XAMPP Control Panel.
3. Copy the `jazzco` folder into:
   - Windows: `C:\xampp\htdocs\jazzco`
   - macOS/Linux XAMPP: `/Applications/XAMPP/htdocs/jazzco` or `/opt/lampp/htdocs/jazzco`
4. Open phpMyAdmin:
   - `http://localhost/phpmyadmin`
5. Click **Import**.
6. Choose `database.sql` from this folder.
7. Click **Go**.
8. Visit:
   - Website: `http://localhost/jazzco/`
   - Player: `http://localhost/jazzco/player.php`
   - Admin: `http://localhost/jazzco/admin/login.php`

## Database connection

The default connection is configured for XAMPP:

```php
DB_HOST = localhost
DB_NAME = jazzco_db
DB_USER = root
DB_PASS = empty
```

Edit `includes/config.php` if your MySQL username/password is different.

## Upload notes

- MP3 uploads are stored in `uploads/songs/`.
- Cover/profile uploads are stored in `uploads/covers/` and `uploads/profiles/`.
- Maximum song upload size is configured in `includes/functions.php`.
- If large files fail, increase these in `php.ini`:
  - `upload_max_filesize`
  - `post_max_size`
  - `max_execution_time`

## Features included

- Landing page with animated hero, trending songs, artists, CTA, footer.
- User registration/login/logout with password hashing and remember-me cookie.
- PHP session management and secure helper functions.
- User profile, profile picture upload, account updates, favorites, recently played.
- Responsive music player with queue, shuffle, repeat, volume, progress, fullscreen style view, mini player, keyboard shortcuts.
- AJAX live search for songs, artists, albums, and playlists.
- Playlist CRUD API with create/delete/add/remove/toggle privacy.
- Favorites API.
- Recently played tracking.
- Admin dashboard with statistics.
- Admin song uploads with MP3/cover validation.
- Admin users, playlists, genres, and settings management.
- MySQL schema with primary keys, foreign keys, relationships, and timestamps.

## File structure

```text
/jazzco
├── /assets
│   ├── /css
│   ├── /js
│   ├── /images
│   ├── /songs
│   └── /icons
├── /admin
├── /includes
├── /uploads
├── index.php
├── login.php
├── register.php
├── profile.php
├── player.php
├── database.sql
└── README.md
```


## Admin CRUD Update

The admin dashboard includes full CRUD pages for:

- Songs: create/upload, read, update metadata/files/covers, delete.
- Artists: create, read, update, delete, and upload artist photos.
- Albums: create, read, update, delete, and upload album covers.
- Users: create, read, update, delete, and update profile pictures/passwords.
- Playlists: create, read, update, delete, and manage playlist songs.
- Genres: create, read, update, delete.

Artist photos are stored inside:

```text
uploads/artists/
```

The artist photo path is saved in the existing `artists.image_path` column.

## macOS XAMPP upload fix

If the website works but **Admin → Songs → Upload song** fails on a MacBook, open this diagnostic page:

```text
http://localhost/jazzco/setup_check.php
```

If any upload folder says **Fix needed**, run this command in Terminal:

```bash
chmod -R 777 /Applications/XAMPP/xamppfiles/htdocs/jazzco/uploads
```

Then restart Apache from XAMPP Manager.

For large MP3 files, edit:

```text
/Applications/XAMPP/xamppfiles/etc/php.ini
```

Use these values, save the file, and restart Apache:

```ini
file_uploads = On
upload_max_filesize = 100M
post_max_size = 120M
max_execution_time = 300
memory_limit = 256M
```

This version also accepts common macOS/XAMPP MP3 MIME detections such as `audio/x-mp3` and `application/octet-stream` when the uploaded file extension is `.mp3`.

## User Albums + Dark/Light Mode Update

This version adds:

- A public Albums page: `http://localhost/jazzco/albums.php`
- An individual album page: `http://localhost/jazzco/album.php?id=1`
- Logged-in users can create albums with title, artist name, release year, and cover image.
- Album owners can edit album details, replace covers, delete albums, and attach existing songs to their album.
- Album cards in the library now open the album page.
- Live search album results now open the matching album.
- The website now has a real dark/light mode switch saved in the browser with `localStorage`.

If you already imported an older JazzCO database, import this file in phpMyAdmin once:

```text
upgrade_add_user_albums.sql
```

If you import `database.sql` fresh, you do not need the upgrade file.

## Player Queue + Global Playback Update

This version adds a global bottom music player across the website. The player saves the current queue, selected song, progress, volume, shuffle, and repeat settings in the browser using localStorage. This lets the user continue listening while moving between JazzCO pages.

Because JazzCO is a classic PHP multi-page website, opening a new page reloads the document. The player restores the last song and time automatically after the new page loads. If the browser blocks autoplay after page navigation, press the Play button once.

Users can now:

- Add songs to a queue from song cards.
- Open the queue from the bottom player.
- Remove songs from the queue.
- Clear the queue.
- Create playlists from `playlists.php`.
- Open playlist pages using `playlist.php?id=ID`.
- Add songs to playlists from the Playlist button on song cards.
- Create a playlist directly from the add-to-playlist modal.
- Upload playlist cover images.
