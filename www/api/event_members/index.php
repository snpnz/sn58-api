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
		 *   path="/api/event_point_members/",
		 *   summary="Get event points members data",
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

		if (!isset($_GET['id_event'])) { die(err('id_event is required')); }

		$file = __DIR__.'/sql/getEventPointMembers.sql';
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
			*   path="/api/event_point_members/",
			*   summary="Add event point member",
			*   @OA\RequestBody(
			*     @OA\JsonContent(
			*       examples={
			*         @OA\Examples(
			*           summary="add event point member model",
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

			$id_event = $mysqli -> real_escape_string($params['id_event']);
			$id_user = $mysqli -> real_escape_string($params['id_user']);
			$name = $mysqli -> real_escape_string($params['name']);
			$surname = $mysqli -> real_escape_string($params['surname']);
			$token = md5("snpnz-invite".time().$uid);

			$file = __DIR__.'/sql/canChangeEventMember.sql';
			$sql = getSql($file, array('id_user' => $uid, 'id_event' => $id_event));
			$q = $mysqli->query($sql);
			if (!$q) { die(err('Error add event', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }
			if ($q -> num_rows == 0) { die(err('Access denide')); }


			$file = __DIR__.'/sql/alreadyMemberChecker.sql';
			$sql = getSql($file, array('id_user' => $uid, 'id_event' => $id_event));
			$q = $mysqli->query($sql);
			if (!$q) { die(err('Error add event', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }
			if ($q -> num_rows == 1) { die(err('Already added')); }

			$file = __DIR__.'/sql/addEventPointMember.sql';
			$sql = getSql(
				$file,
				array(
					'id_author' => $uid,
					'id_user' => $id_user,
					'id_event' => $id_event,
					'name' => nullOrStringInQuotes($name),
					'surname' => nullOrStringInQuotes($surname),
					'token' => nullOrStringInQuotes($token),
					'accepted_at' => nullOrStringInQuotes($id_user == $uid ? date('Y-m-d H:i:s') : null)
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
			 *   path="/api/event_point_members/",
			 *   summary="Del event point member",
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

			$file = __DIR__.'/sql/canDelEventMember.sql';
			$sql = getSql($file, array('id_user' => $uid,  'id' => $id));
			$q = $mysqli->query($sql);
			if (!$q) { die(err('Error add event', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }
			if ($q -> num_rows == 0) { die(err('Access denide')); }


			$file = __DIR__.'/sql/deleteEventMember.sql';
			$sql = getSql($file, array('id' => $id));
			$q = $mysqli->query($sql);
			if (!$q) { die(err('Error del', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }

			exit(jout(array('success' => true, 'id' => $id)));

		break;
        default:
            die(err('Unsupported Method', $_SERVER['REQUEST_METHOD']));
    }
