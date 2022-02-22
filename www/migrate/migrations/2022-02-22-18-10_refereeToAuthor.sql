ALTER TABLE `points_reports` DROP `id_event_point_referee`;
ALTER TABLE `points_reports` ADD `id_author` INT(9) NULL DEFAULT NULL AFTER `id_event`;
ALTER TABLE `points_reports`
    ADD CONSTRAINT `fk_points_reports_id_author`
    FOREIGN KEY (`id_author`)
    REFERENCES `users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE;