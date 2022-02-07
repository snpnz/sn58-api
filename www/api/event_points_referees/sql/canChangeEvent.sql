SELECT
    events.`id`
FROM
    event_points
    LEFT JOIN events ON event_points.id_event = events.id
WHERE
    event_points.id = @id_event_point
    AND (
        events.`id_author` = @id_user
        OR
        (SELECT superman FROM users WHERE id = @id_user) = 1
    )
