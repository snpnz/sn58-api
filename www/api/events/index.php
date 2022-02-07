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
			 *   path="/api/events/",
			 *   summary="Get events data",
			 *   @OA\Response(
			 *     response="200",
			 *     description="List of events",
			 *     @OA\JsonContent(
			 *      examples={
			 *        @OA\Examples(
			 *          summary="Response with ok result",
			 *          value={
			 *            "data":{
			 *              "id": "1", 
			 *              "name": "Изи катка", 
			 *              "description": "Бросай курить - вставай на лыжи", 
			 *              "start_at": "2022-01-25 19:00:00", 
			 *              "finish_at": "2022-01-26 01:00:00", 
			 *              "created_at": "2022-01-24 19:43:23", 
			 *              "id_author": "1",
			 *              "author_name": "Иван",
			 *              "author_surname": "Иванов",
			 *              "author_photo": "https://userphoto.ru/1.jpg"
			 *            }
			 *          }
			 *        )
			 *      }
			 *    )
			 *  )
			 * )
			 */


			$file = __DIR__.'/sql/getEvents.sql';
			$sql = getSql($file);


			$q = $mysqli->query($sql);
			if (!$q) {die(err('Error reading events data', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }
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
			*   path="/api/events/",
			*   summary="Add event",
			*   @OA\RequestBody(
			*     @OA\JsonContent(
			*       examples={
			*         @OA\Examples(
			*           summary="Full model",
			*           value={
			*             "name": "Изи катка", 
			*             "description": "Бросай курить - вставай на лыжи", 
			*             "start_at": "2022-01-25 19:00:00", 
			*             "finish_at": "2022-01-26 01:00:00"
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
			*             "error": "Error add request",
			*             "data": { "message": "description of error" }
			*           }
			*         )
			*       }
			*     )
			*    )
			* )
			* */

			$params = json_decode(file_get_contents('php://input'), true);

			$name = $mysqli -> real_escape_string($params['name']);
			$description = $mysqli -> real_escape_string($params['description']);
			$start_at = $mysqli -> real_escape_string($params['start_at']);
			$finish_at = $mysqli -> real_escape_string($params['finish_at']);

			$file = __DIR__.'/sql/addEvent.sql';
			$sql = getSql(
				$file,
				array(
					'id_user' => $uid,
					'name' => $name,
					'description' => $description,
					'start_at' => $start_at,
					'finish_at' => $finish_at,
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
			 *   path="/api/events/",
			 *   summary="patch event",
			 *   @OA\RequestBody(
			 *     @OA\JsonContent(
			 *       examples={
			 *         @OA\Examples(
			 *           summary="Full model",
			 *           value={
			 *             "id": "1", 
		  	 *             "name": "Изи катка", 
		  	 *             "description": "Бросай курить - вставай на лыжи", 
		  	 *             "start_at": "2022-01-25 19:00:00", 
		  	 *             "finish_at": "2022-01-26 01:00:00"
			 *           }
			 *         )
			 *       }
			 *     )
			 *  ),
			 *  @OA\Response(
			 *    response="200",
			 *    description="Response with ok|fail result",
			 *    @OA\JsonContent(
			 *    	examples={
			 *        @OA\Examples(
			 *          summary="Response with ok result",
			 *          value={
			 *            "success": true,
			 *            "id": 2
			 *          }
			 *        ),
			 *        @OA\Examples(
			 *          summary="Patching error",
			 *          value={
			 *            "error": "Error patch request",
			 *            "data": { "message": "description of error" }
			 *          }
			 *        )
			 *      }
			 *    )
			 *  )
			* )
			* */

			$params = json_decode(file_get_contents('php://input'), true);

			$id = $mysqli -> real_escape_string($params['id']);
			$name = $mysqli -> real_escape_string($params['name']);
			$description = $mysqli -> real_escape_string($params['description']);
			$start_at = $mysqli -> real_escape_string($params['start_at']);
			$finish_at = $mysqli -> real_escape_string($params['finish_at']);

			$file = __DIR__.'/sql/canChangeEvent.sql';
			$sql = getSql($file, array('id_user' => $uid, 'id_event' => $id));
			$q = $mysqli->query($sql);
			if (!$q) { die(err('Error add event', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }
			if ($q -> num_rows == 0) { die(err('Access denide')); }

			$file = __DIR__.'/sql/updateEvent.sql';
			$sql = getSql(
				$file,
				array(
					'id' => $id,
					'id_user' => $uid,
					'name' => $name,
					'description' => $description,
					'start_at' => $start_at,
					'finish_at' => $finish_at,
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
			 *   path="/api/events/",
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
			 *     	examples={
			 *         @OA\Examples(
			 *         summary="Response with ok result",
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
			$sql = getSql($file, array('id_user' => $uid, 'id_event' => $id));
			$q = $mysqli->query($sql);
			if (!$q) { die(err('Error add event', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }
			if ($q -> num_rows == 0) { die(err('Access denide')); }

			$file = __DIR__.'/sql/deleteEvent.sql';
			$sql = getSql(
				$file,
				array(
					'id' => $id
				)
			);
			$q = $mysqli->query($sql);
			if (!$q) { die(err('Error del event', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }

			exit(jout(array('success' => true, 'id' => $id)));

		break;
        default:
            die(err('Unsupported Method', $_SERVER['REQUEST_METHOD']));
    }
