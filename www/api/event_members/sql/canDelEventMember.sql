SELECT
    events.`id`
FROM
    event_members
    LEFT JOIN events ON event_points.id_event = events.id
WHERE
    event_members.id = @id
    AND (
        events.`id_author` = @id_user
        OR
        (SELECT superman FROM users WHERE id = @id_user) = 1
    )
