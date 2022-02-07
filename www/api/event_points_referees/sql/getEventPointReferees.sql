SELECT
    event_points_referees.`id`,
    event_points_referees.`id_event_point`,
    event_points_referees.`id_user`,
    event_points_referees.`created_at`,
    event_points_referees.`id_author`,
    users.name,
    users.surname,
    users.photo
FROM
    `event_points_referees`
LEFT JOIN users ON users.id = event_points_referees.`id_user`
WHERE
    event_points_referees.`id_event_point` = @id_event_point