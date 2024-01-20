SELECT
    event_members.id_event,
    event_members.name,
    event_members.surname,
    event_members.id_user
    event_members.`id`,
    event_members.`created_at`,
    event_members.`id_author`,
    event_members.`login`,
    event_members.`token`,
    event_members.`accepted_at`
FROM event_members
WHERE token=@token
LIMIT 1
