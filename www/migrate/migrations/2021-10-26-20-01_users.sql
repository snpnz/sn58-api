CREATE TABLE IF NOT EXISTS `users` (
    `id` INT(9) NOT NULL AUTO_INCREMENT ,
    `login` VARCHAR(64) NOT NULL,
    `name` VARCHAR(64) NOT NULL,
    `surname` VARCHAR(64) DEFAULT NULL,
    `photo` VARCHAR(255) DEFAULT NULL,
    `password` VARCHAR(255) NOT NULL,
    `strava_id` VARCHAR(255) DEFAULT NULL,
    `register_date` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;
