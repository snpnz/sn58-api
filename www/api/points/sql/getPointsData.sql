SELECT
    points.id,
    points.id_point_group,
    points.name,
    points.point,
    points.description,
    points.code,
    points.created_at,
    points.updated_at,
    points.id_author,
    points_groups.name AS group_name,
    points_groups.description as group_description
FROM
    `points` LEFT JOIN `points_groups` ON points_groups.id = points.id_point_group
WHERE
    1
    ORDER BY points.id_point_group DESC, points.created_at