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
		 *   tags={"Events"},
		 *   path="/api/event_point_referees/",
		 *   summary="Get event points referees data",
		 *   @OA\Parameter(
		 *     name="id_event_point",
		 *     in="query",
		 *     description="event point id",
		 *     required=true,
		 *     style="form"
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="List of event points",
         *     @OA\JsonContent(
         *       examples={
         *         @OA\Examples(
         *           summary="Response with ok result",
         *           value={
         *             "data":{
    	 *               "id":"1",
    	 *               "id_event_point":"2",
    	 *               "id_user":"1",
    	 *               "created_at":"2022-01-02 12:23:23",
    	 *               "id_author":"1",
    	 *               "name":"Иван",
    	 *               "surname":"Иванов",
    	 *               "photo":"http://photos.ru/photo1.jpg"
         *             }
         *           }
         *         )
         *       }
         *     )
		 *   )
		 * )
		 */

		if (!isset($_GET['id_event_point'])) { die(err('id_event_point is required')); }

		$file = __DIR__.'/sql/getEventPointReferees.sql';
		$sql = getSql($file, array('id_event_point' => intval($_GET['id_event_point'])));

		$q = $mysqli->query($sql);
		if (!$q) { die(err('Error reading events data', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }
		$res = array();
		while($r = $q->fetch_assoc()) {
			$res[] = $r;
		}
		exit(data($res));

		break;
		case 'POST':
			require_once('../../_includes/keeper.php');
			/**
			* @OA\Post(
			*   tags={"Events"},
			*   path="/api/event_point_referees/",
			*   summary="Add event point referee",
			*   @OA\RequestBody(
			*     @OA\JsonContent(
			*       examples={
			*         @OA\Examples(
			*           summary="add event point referee model",
			*           value={
			*             "id_event_point": "1", 
			*             "id_user": "2", 
			*             "id_author": "3"
			*           }
			*         )
			*       }
			*     )
			*   ),
			*   @OA\Response(
			*     response="200",
			*     description="Response with ok|fail result",
			*     @OA\JsonContent(
			*       examples={
			*         @OA\Examples(
			*           summary="Response with ok result",
			*           value={
			*             "success": true,
			*             "id": 2
			*           }
			*         ),
			*         @OA\Examples(
			*           summary="Adding error",
			*           value={
			*             "error": "Error add",
			*             "data": { "message": "description of error" }
			*           }
			*         )
			*       }
			*     )
			*   )
			* )
			* */

			$params = json_decode(file_get_contents('php://input'), true);

			$id_event_point = $mysqli -> real_escape_string($params['id_event_point']);
			$id_user = $mysqli -> real_escape_string($params['id_user']);

			$file = __DIR__.'/sql/canChangeEvent.sql';
			$sql = getSql($file, array('id_user' => $uid, 'id_event_point' => $id_event_point));
			$q = $mysqli->query($sql);
			if (!$q) { die(err('Error add event', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }
			if ($q -> num_rows == 0) { die(err('Access denide')); }

			$file = __DIR__.'/sql/addEventPointReferee.sql';
			$sql = getSql(
				$file,
				array(
					'id_author' => $uid,
					'id_user' => $id_user,
					'id_event_point' => $id_event_point
				)
			);
			$q = $mysqli->query($sql);
			if (!$q) { die(err('Error add', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }

			exit(jout(array('success' => true, 'id' => $mysqli->insert_id)));
	
		break;
		
		case 'DELETE':
			require_once('../../_includes/keeper.php');
			/**
			 * @OA\Delete(
			 *   tags={"Events"},
			 *   path="/api/event_point_referees/",
			 *   summary="Del event point referee",
			 *   @OA\RequestBody(
			 *     @OA\JsonContent(
			 *       examples={
			 *         @OA\Examples(
			 *           summary="Full model",
			 *           value={
			 *             "id": "1"
			 *           }
			 *         )
			 *       }
			 *     )
			 *   ),
			 *   @OA\Response(
			 *     response="200",
			 *     description="Response with ok|fail result",
			 *     @OA\JsonContent(
			 *       examples={
			 *         @OA\Examples(
			 *           summary="Response with ok result",
			 *           value={
			 *             "success": true
			 *           }
			 *         ),
			 *         @OA\Examples(
			 *           summary="Deleting error error",
			 *           value={
			 *             "error": "Error del request",
			 *             "data": { "message": "description of error" }
			 *           }
			 *         )
			 *       }
			 *     )
			 *   )
			 * )
			 * */

			$params = json_decode(file_get_contents('php://input'), true);

			$id = $mysqli -> real_escape_string($params['id']);

			$file = __DIR__.'/sql/canChangeEventReferee.sql';
			$sql = getSql($file, array('id_user' => $uid,  'id_event_points_referee' => $id));
			$q = $mysqli->query($sql);
			if (!$q) { die(err('Error add event', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }
			if ($q -> num_rows == 0) { die(err('Access denide')); }


			$file = __DIR__.'/sql/deleteEventPointReferee.sql';
			$sql = getSql($file, array('id' => $id));
			$q = $mysqli->query($sql);
			if (!$q) { die(err('Error del', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }

			exit(jout(array('success' => true, 'id' => $id)));

		break;
        default:
            die(err('Unsupported Method', $_SERVER['REQUEST_METHOD']));
    }
