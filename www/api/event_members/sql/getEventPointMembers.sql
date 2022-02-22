SELECT
    event_members.`id`,
    event_members.`id_event`,
    event_members.`created_at`,
    event_members.`id_author`,
    event_members.`id_user`,
    event_members.`name`,
    event_members.`surname`,
    event_members.`token`,
    event_members.`team`,
    event_members.`accepted_at`,
    users.name as username,
    users.surname as usersurname,
    users.photo as userphoto,
    author.name as authorname,
    author.surname as authorsurname,
    author.photo as authorphoto
FROM
    `event_members`
    LEFT JOIN users ON users.id = event_members.id_user
    LEFT JOIN users as author ON author.id = event_members.id_author
WHERE
    event_members.id_event = @id_event