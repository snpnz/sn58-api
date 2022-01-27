SELECT
    points_reports.`id`,
    points_reports.`id_point`,
    points_reports.`id_user`,
    points_reports.`coordinates`,
    points_reports.`comment`,
    points_reports.`created_at`,
    points_reports.`upload_at`,
    points.name,
    points_groups.name AS group_name,
    points_groups.description as group_description,
    CONCAT_WS(' ', users.surname, users.name) as username,
    users.photo as userphoto
FROM
    `points_reports`
    LEFT JOIN points ON points.id = points_reports.id_point
    LEFT JOIN `points_groups` ON points_groups.id = points.id_point_group
    LEFT JOIN users ON users.id = points_reports.id_user
--@is_filter_by_point WHERE points_reports.id_point = @id_point
ORDER BY points_reports.`created_at` DESC