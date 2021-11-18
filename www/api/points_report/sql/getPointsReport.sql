SELECT
    points_reports.`id`,
    points_reports.`id_point`,
    points_reports.`id_user`,
    points_reports.`coordinates`,
    points_reports.`comment`,
    points_reports.`created_at`,
    points_reports.`upload_at`
FROM
    `points_reports`
WHERE
    points_reports.id_user = @id_user