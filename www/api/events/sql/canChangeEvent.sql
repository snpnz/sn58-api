SELECT
    `id`
FROM
    `events`
WHERE
    id = @id_event
    AND start_at > NOW()
    AND (`id_author` = @id_user OR COUNT(SELECT id FROM users WHERE superman = 1 AND id = @id_user) = 1)