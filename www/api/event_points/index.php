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
		 *   path="/api/event_points/",
		 *   summary="Get event points data",
		 *   @OA\Parameter(
		 *     name="id_event",
		 *     in="query",
		 *     description="event id",
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
    	 *               "id": "1",
    	 *               "id_point_group": "1",
    	 *               "name": "Точка 1",
    	 *               "point": "54.567567,43.75456",
    	 *               "description": "точка один",
    	 *               "code": "d6d56d6f",
    	 *               "created_at": "2021-09-09 12:54:23",
    	 *               "updated_at": "2021-09-09 12:54:23",
    	 *               "id_author": "1",
    	 *               "group_name": "Точки",
    	 *               "group_description": "все точки"
         *             }
         *           }
         *         )
         *       }
         *     )
		 *   )
		 * )
		 */

		if (!isset($_GET['id_event'])) { die(err('id_event is required')); }

		$file = __DIR__.'/sql/getEventPoints.sql';
		$sql = getSql($file, array('id_event' => intval($_GET['id_event'])));

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
			*   path="/api/event_points/",
			*   summary="Add event point",
			*   @OA\RequestBody(
			*     @OA\JsonContent(
			*       examples={
			*         @OA\Examples(
			*           summary="add event point model",
			*           value={
			*             "id_event": "1", 
			*             "id_point": "2", 
			*             "sort_order": "0"
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

			$id_event = $mysqli -> real_escape_string($params['id_event']);
			$id_point = $mysqli -> real_escape_string($params['id_point']);
			$sort_order = $mysqli -> real_escape_string($params['sort_order']);

			$file = __DIR__.'/sql/canChangeEventByIdEvent.sql';
			$sql = getSql($file, array('id_user' => $uid, 'id_event' => $id_event));
			$q = $mysqli->query($sql);
			if (!$q) { die(err('Error add event', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }
			if ($q -> num_rows == 0) { die(err('Access denide')); }

			$file = __DIR__.'/sql/addEventPoint.sql';
			$sql = getSql(
				$file,
				array(
					'id_user' => $uid,
					'id_point' => $id_point,
					'id_event' => $id_event,
					'sort_order' => $sort_order,
				)
			);
			$q = $mysqli->query($sql);
			if (!$q) { die(err('Error add event', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }

			exit(jout(array('success' => true, 'id' => $mysqli->insert_id)));
	
		break;
		case 'PATCH':
			require_once('../../_includes/keeper.php');
			/**
			 * @OA\Patch(
			 *   tags={"Events"},
			 *   path="/api/event_points/",
			 *   summary="patch event point",
			 *   @OA\RequestBody(
			 *     @OA\JsonContent(
			 *     examples={
			 *       @OA\Examples(
			 *         summary="Full model",
			 *         value={
			 *           "sort_order": "0"
			 *         }
			 *       )
			 *     }
			 *    )
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
			 *           summary="Patching error",
			 *           value={
			 *             "error": "Error patch request",
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
			$id_event = $mysqli -> real_escape_string($params['id_event']);
			$sort_order = $mysqli -> real_escape_string($params['sort_order']);

			$file = __DIR__.'/sql/canChangeEvent.sql';
			$sql = getSql($file, array('id_user' => $uid, 'id_event_point' => $id));
			$q = $mysqli->query($sql);
			if (!$q) { die(err('Error add event', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }
			if ($q -> num_rows == 0) { die(err('Access denide')); }

			$file = __DIR__.'/sql/updateEventPoint.sql';
			$sql = getSql(
				$file,
				array(
					'id' => $id,
					'id_user' => $uid,
					'sort_order' => $sort_order,
				)
			);
			$q = $mysqli->query($sql);
			if (!$q) { die(err('Error add event', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }

			exit(jout(array('success' => true, 'id' => $id)));
	
		break;
		case 'DELETE':
			require_once('../../_includes/keeper.php');
			/**
			 * @OA\Delete(
			 *   tags={"Events"},
			 *   path="/api/event_points/",
			 *   summary="Del event",
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

			$file = __DIR__.'/sql/canChangeEvent.sql';
			$sql = getSql($file, array('id_user' => $uid, 'id_event_point' => $id));
			$q = $mysqli->query($sql);
			if (!$q) { die(err('Error add event', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }
			if ($q -> num_rows == 0) { die(err('Access denide')); }


			$file = __DIR__.'/sql/deleteEventPoint.sql';
			$sql = getSql($file, array('id' => $id));
			$q = $mysqli->query($sql);
			if (!$q) { die(err('Error del', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }

			exit(jout(array('success' => true, 'id' => $id)));

		break;
        default:
            die(err('Unsupported Method', $_SERVER['REQUEST_METHOD']));
    }
