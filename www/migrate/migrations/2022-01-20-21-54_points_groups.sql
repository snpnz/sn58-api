CREATE TABLE `points_groups` (
    `id` INT(8) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `created_at` DATETIME NULL DEFAULT NULL,
    `updated_at` DATETIME NULL DEFAULT NULL,
    `id_author` INT(9) NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB COMMENT = 'Группы точек';

ALTER TABLE `points_groups`
    ADD CONSTRAINT `fk_user_points_groups_id_author`
    FOREIGN KEY (`id_author`)
    REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;

ALTER TABLE `points` ADD `id_point_group` INT(8) NULL DEFAULT NULL AFTER `id`;

ALTER TABLE `points`
    ADD CONSTRAINT `fk_points_id_point_group`
    FOREIGN KEY (`id_point_group`)
    REFERENCES `points_groups` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE;