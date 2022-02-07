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
    users.photo as author_photo
FROM
    `events`
    LEFT JOIN users ON users.id = events.id_author
WHERE
    1
ORDER BY events.start_at DESC