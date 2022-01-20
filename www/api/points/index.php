<?php
	require_once('../../_includes/resp.php');
	require_once('../../_includes/db.php');
	require_once('../../_includes/sql.php');
	require_once('../../_includes/cors.php');

	switch($_SERVER['REQUEST_METHOD'])
	{
		case 'GET':
		/**
		 * @OA\Get(
		 *   tags={"Points"},
		 *   path="/api/points/",
		 *   summary="Get points data",
		 *   @OA\Response(
		 *     response="200",
		 *     description="List of points",
         *       @OA\JsonContent(
         *         examples={
         *           @OA\Examples(
         *             summary="Response with ok result",
         *             value={
         *               "data":{
         *                 "id":"4",
		 * 				   "id_point_group":"1",
		 * 				   "group_name":"1",
		 * 				   "group_description":"1",
         *                 "login":"sn58",
         *                 "name":"\u0421\u0435\u0440\u0435\u0431\u0440\u044f\u043d\u0430\u044f",
         *                 "surname":"\u041d\u0438\u0442\u044c",
         *                 "photo":"https:\/\/lh3.googleusercontent.com\/a\/AATXAJzuWNpml3tO9tGWxiLp3GTrf4DLoycUjPpNzUXm=s96-c",
         *                 "strava_id":"94556245",
         *                 "register_date":"2021-10-26 20:58:26"
         *               }
         *             }
         *           )
         *         }
         *       )
		 *     )
		 * )
		 */


		$file = __DIR__.'/sql/getPointsData.sql';
		$sql = getSql($file, array());
		$q = $mysqli->query($sql);
		if (!$q) { die(err('Error reading points data', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }
		$res = array();
		while($r = $q->fetch_assoc()) {
			$res[] = $r;
		}
		exit(data($res));

		break;
		
        default:
            die(err('Unsupported Method', $_SERVER['REQUEST_METHOD']));
    }
