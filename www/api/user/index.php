<?php
	require_once('../../_includes/resp.php');
	require_once('../../_includes/db.php');
	require_once('../../_includes/sql.php');
	require_once('../../_includes/keeper.php');
	require_once('../../_includes/cors.php');

	switch($_SERVER['REQUEST_METHOD'])
	{
		case 'GET':
			/**
			 * @OA\Get(
			 *     tags={"User"},
			 *     path="/api/user/",
			 *     summary="Get user data",
			 *     @OA\Response(
			 *         response="200",
			 *         description="List of callrequests",
         *                 @OA\JsonContent(
         *                		examples={
         *                         @OA\Examples(
         *                              summary="Response with ok result",
         *                        		value={
         *                                 "data":{
         *                                    "id":"4",
         *                                    "login":"sn58",
         *                                    "name":"\u0421\u0435\u0440\u0435\u0431\u0440\u044f\u043d\u0430\u044f",
         *                                    "surname":"\u041d\u0438\u0442\u044c",
         *                                    "photo":"https:\/\/lh3.googleusercontent.com\/a\/AATXAJzuWNpml3tO9tGWxiLp3GTrf4DLoycUjPpNzUXm=s96-c",
         *                                    "strava_id":"94556245",
         *                                    "register_date":"2021-10-26 20:58:26"
         *                                 }
         *                              }
         *                         )
         *                     }
         *                 )
			 *
			 *     )
			 * )
			 */


			$file = __DIR__.'/sql/getUserData.sql';
			$sql = getSql($file, array('id_user' => isset($_GET['id']) ? intval($_GET['id']) : $uid));
			$q = $mysqli->query($sql);
			if (!$q) { die(err('Error reading user data', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }
			$r = $q->fetch_assoc();
			exit(data($r));

			break;
		
        default:
            die(err('Unsupported Method', $_SERVER['REQUEST_METHOD']));
    }
