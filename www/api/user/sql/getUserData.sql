SELECT
    users.`id`,
    users.`login`,
    users.`name`,
    users.`surname`,
    users.`photo`,
    users.`strava_id`,
    users.`register_date`,
    COUNT(SELECT deadline FROM referees WHERE id_user=users.id AND deadline >= NOW() AND created_at <= NOW() LIMIT 1) as is_referee
FROM
    `users`
WHERE
    users.id = @id_user
