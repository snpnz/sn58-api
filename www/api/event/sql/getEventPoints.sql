SELECT
    event_points.id,
    points.name,
    points.point as coords,
    event_points.id_point,
    event_points_referees.id as id_event_points_referee,
    users.id as referee_id,
    users.name as referee_name,
    users.surname as referee_surname,
    users.photo as referee_photo

FROM
    event_points
    LEFT JOIN points ON event_points.id_point = points.id
    LEFT JOIN event_points_referees ON event_points_referees.id_event_point = event_points.id
    LEFT JOIN users ON users.id = event_points_referees.id_user
WHERE
    event_points.id_event = @id_event
ORDER BY event_points.sort_order, event_points.id