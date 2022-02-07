<?php
	require_once('../../_includes/resp.php');
	require_once('../../_includes/db.php');
	require_once('../../_includes/sql.php');
	require_once('../../_includes/cors.php');
	require_once('../../_includes/keeper.php');

	switch($_SERVER['REQUEST_METHOD'])
	{
		case 'GET':
		/**
		 * @OA\Get(
		 *     tags={"Event"},
		 *     path="/api/event/",
		 *     summary="Get one event data",
		 * 	   @OA\Parameter(
		 *       name="id",
		 *       in="query",
		 *       description="event id",
		 *       required=true,
		 *       style="form"
		 *     ),
		 *     @OA\Response(
		 *         response="200",
		 *         description="One event data",
         *                 @OA\JsonContent(
         *                		examples={
         *                         @OA\Examples(
         *                              summary="Response with ok result",
         *                        		value={
         *                                 "data":{
         *										"id": "1", 
         *										"name": "Изи катка", 
         *										"description": "Бросай курить - вставай на лыжи", 
         *										"start_at": "2022-01-25 19:00:00", 
         *										"finish_at": "2022-01-26 01:00:00", 
         *										"created_at": "2022-01-24 19:43:23", 
         *										"id_author": "1",
         *										"author_name": "Иван",
         *										"author_surname": "Иванов",
         *										"author_photo": "https://userphoto.ru/1.jpg",
         *                                 }
         *                              }
         *                         )
         *                     }
         *                 )
		 *
		 *     )
		 * )
		 */

		if (!isset($_GET['id'])) { die(err('id is required')); }

			$file = __DIR__.'/sql/getEvent.sql';
			$criterias = array(
				'id' => intval($_GET['id'])
			);

			$sql = getSql($file, $criterias);
			$q = $mysqli->query($sql);
			if (!$q) { die(err('Error reading events data', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }
			$result = $q->fetch_assoc();
			$result['points'] = array();

			$file = __DIR__.'/sql/getEventPoints.sql';
			$criterias = array(
				'id_event' => intval($_GET['id'])
			);

			$sql = getSql($file, $criterias);
			$q = $mysqli->query($sql);
			if (!$q) { die(err('Error reading events data', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }
			while($r = $q->fetch_assoc()) {
				$result['points'][] = $r;
			};
			
			exit(data($result));

		break;
		
        default:
            die(err('Unsupported Method', $_SERVER['REQUEST_METHOD']));
    }
