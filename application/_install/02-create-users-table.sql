-- SQLite version.
CREATE TABLE IF NOT EXISTS `users` (
        `user_id` INTEGER PRIMARY KEY AUTOINCREMENT,
        `user_name` VARCHAR(64),
        `user_password_hash` VARCHAR(255) DEFAULT '!', -- invalid hash
        `user_email` VARCHAR(64),
        `user_rank` INTEGER NOT NULL DEFAULT 1,
        `psw_expire` date DEFAULT (date('now','+1 month','localtime'))
        -- `psw_expire` DATETIME DEFAULT date('now','+1 month')
);
CREATE UNIQUE INDEX IF NOT EXISTS `user_name_UNIQUE` ON `users` (`user_name` ASC);
CREATE UNIQUE INDEX IF NOT EXISTS `user_email_UNIQUE` ON `users` (`user_email` ASC);

CREATE TRIGGER set_psw_expire UPDATE OF user_password_hash ON users
  BEGIN
    UPDATE users SET psw_expire = date('now','+1 month','localtime') WHERE user_id = OLD.user_id;
  END;

-- Default users
INSERT OR IGNORE INTO "users" VALUES(1,'admin','$2y$10$vmQOBZAfZKTMARumrOeNp.skPGojuMSes.vjvZGOy0ljMtKcfqo.e','admin@whoknows.com',0,'2022-07-26');




-- MySQL version (TO BE TESTED)
-- CREATE TABLE IF NOT EXISTS `charisma-mvc`.`users` (
--         `user_id` INTEGER PRIMARY KEY AUTO_INCREMENT,
--         `user_name` varchar(64),
--         `user_password_hash` varchar(255),
--         `user_email` varchar(64)
-- );
-- CREATE UNIQUE INDEX `user_name_UNIQUE` ON `users` (`user_name` ASC);
-- CREATE UNIQUE INDEX `user_email_UNIQUE` ON `users` (`user_email` ASC);
