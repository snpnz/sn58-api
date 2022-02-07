INSERT INTO `events`(
    `name`,
    `description`,
    `start_at`,
    `finish_at`,
    `created_at`,
    `id_author`
)
VALUES(
    '@name',
    '@description',
    '@start_at',
    '@finish_at',
    NOW(),
    @id_user
);