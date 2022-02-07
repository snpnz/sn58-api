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
    event_points
    LEFT JOIN `points` ON event_points.id_point = points.id
    LEFT JOIN `points_groups` ON points_groups.id = points.id_point_group
WHERE
    event_points.id_event = @id_event
ORDER BY event_points.sort_order