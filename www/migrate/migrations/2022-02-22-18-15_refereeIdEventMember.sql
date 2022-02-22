ALTER TABLE `points_reports` ADD `id_event_member` INT(9) NULL DEFAULT NULL AFTER `id_author`;
ALTER TABLE `points_reports`
    ADD CONSTRAINT `fk_points_reports_id_event_member`
    FOREIGN KEY (`id_event_member`)
    REFERENCES `event_members` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE;