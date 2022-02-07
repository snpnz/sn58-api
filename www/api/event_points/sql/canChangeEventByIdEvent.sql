SELECT
    `id`
FROM
    `events`
WHERE
    id = @id_event
    AND start_at > NOW()
    AND (`id_author` = @id_user OR (SELECT superman FROM users WHERE id = @id_user) = 1)