SELECT
    points_reports.`id`,
    points_reports.`id_point`,
    points_reports.`id_user`,
    points_reports.`coordinates`,
    points_reports.`comment`,
    points_reports.`created_at`,
    points_reports.`upload_at`,
    points.name
FROM
    `points_reports`
    LEFT JOIN points ON points.id = points_reports.id_point
WHERE
    points_reports.id_user = @id_user
ORDER BY points_reports.`created_at` DESC