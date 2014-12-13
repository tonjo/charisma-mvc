
-- BASIC access log

CREATE TABLE IF NOT EXISTS `log` (
        `id` INTEGER PRIMARY KEY AUTOINCREMENT,
        `user_email` VARCHAR(64),   -- NOT enforcing reference integrity (if a user is deleted...)
        `with_password` BOOLEAN,    -- If false maybe it's a cookie login (remember me)
        `from_ip`  VARCHAR(64),
        `last_login` DATETIME NOT NULL DEFAULT (datetime('now','localtime')),
        `last_logout` DATETIME
);
