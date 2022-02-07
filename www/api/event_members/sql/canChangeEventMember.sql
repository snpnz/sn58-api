SELECT
    events.`id`
FROM
    events
WHERE
    events.id = @id_event
    AND events.start_at > NOW()
