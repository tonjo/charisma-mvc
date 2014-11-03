-- SQLite version.
CREATE TABLE IF NOT EXISTS `users` (
        `user_id` INTEGER PRIMARY KEY AUTOINCREMENT,
        `user_name` VARCHAR(64),
        `user_password_hash` VARCHAR(255),
        `user_email` VARCHAR(64),
        `user_rank` INTEGER NOT NULL DEFAULT 1,
        `psw_expire` datetime DEFAULT CURRENT_DATE
);
CREATE UNIQUE INDEX `user_name_UNIQUE` ON `users` (`user_name` ASC);
CREATE UNIQUE INDEX `user_email_UNIQUE` ON `users` (`user_email` ASC);

-- MySQL version (TO BE TESTED)
-- CREATE TABLE IF NOT EXISTS `charisma-mvc`.`users` (
--         `user_id` INTEGER PRIMARY KEY AUTO_INCREMENT,
--         `user_name` varchar(64),
--         `user_password_hash` varchar(255),
--         `user_email` varchar(64)
-- );
-- CREATE UNIQUE INDEX `user_name_UNIQUE` ON `users` (`user_name` ASC);
-- CREATE UNIQUE INDEX `user_email_UNIQUE` ON `users` (`user_email` ASC);
