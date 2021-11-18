CREATE TABLE IF NOT EXISTS `points_reports` (
    `id` INT(9) NOT NULL AUTO_INCREMENT ,
    `id_point` INT(9) NOT NULL, 
    `id_user` INT(9) NOT NULL, 
    `coordinates` VARCHAR(64) NOT NULL ,
    `comment` VARCHAR(512) NOT NULL ,
    `created_at` DATETIME NOT NULL ,
    `upload_at` DATETIME NULL DEFAULT NULL , 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB COMMENT = 'Отментки пользователей на точках';

ALTER TABLE `points_reports`
    ADD CONSTRAINT `fk_user_points_reports_id_user`
    FOREIGN KEY (`id_user`)
    REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;

ALTER TABLE `points_reports`
    ADD CONSTRAINT `fk_user_points_reports_id_point`
    FOREIGN KEY (`id_point`)
    REFERENCES `points` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;
