UPDATE
    `events`
SET
    `name` = '@name',
    `description` = '@description',
    `start_at` = '@start_at',
    `finish_at` = '@finish_at'
WHERE
    `id` = @id