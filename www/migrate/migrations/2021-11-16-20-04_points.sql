CREATE TABLE IF NOT EXISTS `points` (
    `id` INT(9) NOT NULL AUTO_INCREMENT ,
    `name` VARCHAR(128) NOT NULL ,
    `point` VARCHAR(64) NOT NULL ,
    `description` VARCHAR(512) NOT NULL ,
    `code` VARCHAR(96) NOT NULL ,
    `created_at` DATETIME NOT NULL ,
    `updated_at` DATETIME NULL DEFAULT NULL ,
    `id_author` INT(9)  NULL DEFAULT NULL, 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB COMMENT = 'Точки';

ALTER TABLE `points`
    ADD CONSTRAINT `fk_user_points_id_author`
    FOREIGN KEY (`id_author`)
    REFERENCES `users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE;
