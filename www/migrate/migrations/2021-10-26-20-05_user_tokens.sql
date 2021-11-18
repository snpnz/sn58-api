CREATE TABLE IF NOT EXISTS `user_tokens` (
    `id` INT(9) NOT NULL AUTO_INCREMENT ,
    `id_user` INT(9) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `refresh_token` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL,
    `expires_at` DATETIME DEFAULT NULL,
    `ip` INT(10) unsigned NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

ALTER TABLE `user_tokens`
    ADD CONSTRAINT `fk_user_tokens_id_user`
    FOREIGN KEY (`id_user`)
    REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;
