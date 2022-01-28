CREATE TABLE `events` (
    `id` INT(8) NOT NULL AUTO_INCREMENT,
    `name` varchar(128) NOT NULL,
    `description` varchar(255) NULL DEFAULT NULL,
    `start_at` DATETIME NOT NULL,
    `finish_at` DATETIME NOT NULL,
    `created_at` DATETIME NULL DEFAULT NULL,
    `id_author` INT(9) NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB COMMENT = 'Мероприятия';

ALTER TABLE `events`
    ADD CONSTRAINT `fk_events_id_author`
    FOREIGN KEY (`id_author`)
    REFERENCES `users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE;

ALTER TABLE `points_reports` CHANGE `id_user` `id_user` INT(9) NULL;
ALTER TABLE `points_reports` ADD `id_event` INT(8) NULL DEFAULT NULL AFTER `id_user`;
ALTER TABLE `points_reports` ADD `id_event_point_referee` INT(8) NULL DEFAULT NULL AFTER `id_event`;

ALTER TABLE `points_reports`
    ADD CONSTRAINT `fk_points_reports_id_event`
    FOREIGN KEY (`id_event`)
    REFERENCES `events` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE;

ALTER TABLE `points_reports`
    ADD CONSTRAINT `fk_points_reports_id_event_point_referee`
    FOREIGN KEY (`id_event_point_referee`)
    REFERENCES `event_points_referees` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE;

CREATE TABLE `event_points` (
    `id` INT(8) NOT NULL AUTO_INCREMENT,
    `id_event` INT(9) NOT NULL,
    `id_point` INT(9) NOT NULL,
    `sort_order` INT(2) DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB COMMENT = 'Точки для мероприятия';

ALTER TABLE `event_points`
    ADD CONSTRAINT `fk_event_points_id_event`
    FOREIGN KEY (`id_event`)
    REFERENCES `events` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;

ALTER TABLE `event_points`
    ADD CONSTRAINT `fk_event_points_id_point`
    FOREIGN KEY (`id_point`)
    REFERENCES `points` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;


CREATE TABLE `event_points_referees` (
    `id` INT(8) NOT NULL AUTO_INCREMENT,
    `id_event_point` INT(9) NOT NULL,
    `id_user` INT(9) NOT NULL,
    `created_at` DATETIME NULL DEFAULT NULL,
    `id_author` INT(9) NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB COMMENT = 'Судьи на точках для мероприятия';

ALTER TABLE `event_points_referees`
    ADD CONSTRAINT `fk_event_points_referees_id_event_point`
    FOREIGN KEY (`id_event_point`)
    REFERENCES `event_points` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;

ALTER TABLE `event_points_referees`
    ADD CONSTRAINT `fk_event_points_referees_id_author`
    FOREIGN KEY (`id_author`)
    REFERENCES `users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE;

ALTER TABLE `event_members`
    ADD CONSTRAINT `fk_event_points_referees_id_user`
    FOREIGN KEY (`id_user`)
    REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;

CREATE TABLE `event_members` (
    `id` INT(8) NOT NULL AUTO_INCREMENT,
    `id_event` INT(9) NOT NULL,
    `created_at` DATETIME NULL DEFAULT NULL,
    `id_author` INT(9) NULL DEFAULT NULL,
    `id_user` INT(9) NULL DEFAULT NULL,
    `name` varchar(64) NULL DEFAULT NULL,
    `surname` varchar(64) NULL DEFAULT NULL,
    `token` varchar(64) NULL DEFAULT NULL,
    `accepted_at` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB COMMENT = 'Участники мероприятия';

ALTER TABLE `event_members`
    ADD CONSTRAINT `fk_events_id_event`
    FOREIGN KEY (`id_event`)
    REFERENCES `events` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;

ALTER TABLE `event_members`
    ADD CONSTRAINT `fk_events_id_author`
    FOREIGN KEY (`id_author`)
    REFERENCES `users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE;

ALTER TABLE `event_members`
    ADD CONSTRAINT `fk_events_id_user`
    FOREIGN KEY (`id_user`)
    REFERENCES `users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE;

ALTER TABLE `points_reports` DROP FOREIGN KEY `fk_points_reports_id_referee`;
ALTER TABLE `points_reports` DROP INDEX `fk_points_reports_id_referee`;
ALTER TABLE `points_reports` DROP `id_referee`;

DROP TABLE `referees`;