SELECT
    events.id,
    events.name,
    events.description,
    events.start_at,
    events.finish_at,
    events.created_at,
    events.id_author,
    users.name as author_name,
    users.surname as author_surname,
    users.photo as author_photo,
    GROUP_CONCAT(
        CONCAT_WS(
            '|',
            event_points.id,
            points.name,
            points.point,
            event_points.id_point
        )
        SEPARATOR '~'
    ) as points
FROM
    `events`
    LEFT JOIN users ON users.id = events.id_author
    LEFT JOIN event_points ON event_points.id_event = events.id
    LEFT JOIN points ON event_points.id_point = points.id
WHERE
    events.id = @id
GROUP BY events.id
LIMIT 1