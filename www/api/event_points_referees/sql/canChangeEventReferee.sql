SELECT
    events.`id`
FROM
    event_points
    LEFT JOIN events ON event_points.id_event = events.id
    LEFT JOIN event_points_referees ON event_points.id = event_points_referees.id_event_point
WHERE
    event_points_referees.id = @id_event_points_referee
    AND (
        events.`id_author` = @id_user
        OR
        (SELECT superman FROM users WHERE id = @id_user) = 1
    )
