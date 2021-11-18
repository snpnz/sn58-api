SELECT
    `id`,
    `login`,
    `name`,
    `surname`,
    `photo`,
    `strava_id`,
    `register_date`
FROM
    `users`
WHERE
    id = @id_user
