SELECT
    users.`id`,
    users.`login`,
    users.`name`,
    users.`surname`,
    users.`photo`,
    users.`strava_id`,
    users.`register_date`,
    0 as is_referee
FROM
    `users`
WHERE
    users.id = @id_user
