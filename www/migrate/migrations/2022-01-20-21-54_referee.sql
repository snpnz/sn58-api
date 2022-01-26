CREATE TABLE `referees` (
    `id` INT(8) NOT NULL AUTO_INCREMENT,
    `id_user` INT(9) NULL DEFAULT NULL,
    `deadline` DATETIME NULL DEFAULT NULL,
    `created_at` DATETIME NULL DEFAULT NULL,
    `id_point_group` INT(8) NULL DEFAULT NULL,
    `id_author` INT(9) NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB COMMENT = 'Судьи';

ALTER TABLE `referees`
    ADD CONSTRAINT `fk_referees_id_user`
    FOREIGN KEY (`id_user`)
    REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;

ALTER TABLE `referees`
    ADD CONSTRAINT `fk_referees_id_author`
    FOREIGN KEY (`id_author`)
    REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;

ALTER TABLE `referees`
    ADD CONSTRAINT `fk_referees_id_pointgroup`
    FOREIGN KEY (`id_point_group`)
    REFERENCES `points_groups` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE;

ALTER TABLE `points_reports` ADD `id_referee` INT(8) NULL DEFAULT NULL AFTER `id_user`;

ALTER TABLE `points_reports`
    ADD CONSTRAINT `fk_points_reports_id_referee`
    FOREIGN KEY (`id_referee`)
    REFERENCES `referees` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE;