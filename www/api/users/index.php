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
			 *   tags={"Users"},
			 *   path="/api/users/",
			 *   summary="Get users data",
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
			 *              "name": "Иван",
			 *              "surname": "Иванов",
			 *              "photo": "https://userphoto.ru/1.jpg"
			 *            }
			 *          }
			 *        )
			 *      }
			 *    )
			 *  )
			 * )
			 */


			$file = __DIR__.'/sql/getUsers.sql';
			$sql = getSql($file);


			$q = $mysqli->query($sql);
			if (!$q) {die(err('Error reading data', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }
			$res = array();
			while($r = $q->fetch_assoc()) {
				$res[] = $r;
			}
			exit(data($res));

			break;
		
        default:
            die(err('Unsupported Method', $_SERVER['REQUEST_METHOD']));
    }
