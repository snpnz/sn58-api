SELECT
    events.`id`
FROM
    event_members
    LEFT JOIN events ON event_members.id_event = events.id
WHERE
    event_members.id_event = @id_event
    AND event_members.id_user = @id_user
