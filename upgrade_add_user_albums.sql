USE jazzco_db;

-- Run this only if you already imported an older JazzCO database.
-- It adds support for user-created albums.
ALTER TABLE albums
    ADD COLUMN user_id INT NULL AFTER id,
    ADD INDEX idx_albums_user (user_id);

ALTER TABLE albums
    ADD CONSTRAINT fk_albums_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE SET NULL;
