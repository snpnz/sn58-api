INSERT INTO `event_points_referees`(
    `id_event_point`,
    `id_user`,
    `created_at`,
    `id_author`
)
VALUES(
    @id_event_point,
    @id_user,
    NOW(),
    @id_author
)