INSERT INTO `points_reports`(
    `id_point`,
    `id_user`,
    `coordinates`,
    `comment`,
    `created_at`,
    `upload_at`
)
VALUES(
    @id_point,
    @id_user,
    '@coordinates',
    '@comment',
    '@created_at',
    NOW()
);
