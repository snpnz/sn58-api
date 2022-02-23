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
		 *     tags={"PointReport"},
		 *     path="/api/points_report/",
		 *     summary="Get points report data",
		 *     @OA\Response(
		 *         response="200",
		 *         description="List of points report",
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


			$file1 = __DIR__.'/sql/getPointsReport.sql';
			$file2 = __DIR__.'/sql/getAllPointsReports.sql';
			$sql = !isset($_GET['all'])
				? getSql($file1, array('id_user' => $uid))
				: getSql($file2, array(
					'is_filter_by_point' => isset($_GET['id_point']), 
					'id_point' => intval($_GET['id_point']), 
				));

			$q = $mysqli->query($sql);
			if (!$q) { die(err('Error reading user data', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file1, '$uid' => $uid))); }
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
				 *     tags={"PointReport"},
				 *     path="/api/points_report/",
				 *     summary="Add point report",
				 *     @OA\RequestBody(
				 *         @OA\JsonContent(
				 *        		examples={
				 *                 @OA\Examples(
				 *                   summary="Full model",
				 *                   value={
		 		 *                     "id_point":"2",
		 		 *                     "coordinates":"53.09351,45.30854",
		 		 *                     "comment":"Ура, я тут",
		 		 *                     "created_at":"2021-11-16 20:23:09"
				 *                   }
				 *                 ),
				 *                 @OA\Examples(
				 *                   summary="Full modeql",
				 *                   value={
		 		 *                     "id_point":"2",
		 		 *                     "coordinates":"53.09351,45.30854",
		 		 *                     "comment":"Ура, я тут",
		 		 *                     "created_at":"2021-11-16 20:23:09"
				 *                   }
				 *                 )
				 *             }
				 *         )
				 *     ),
				 *     @OA\Response(
				 *         response="200",
				 *         description="Response with ok|fail result",
				 *         @OA\JsonContent(
				 *        		examples={
				 *                 @OA\Examples(
				 *                      summary="Response with ok result",
				 *                		value={
				 *                         "success": true, "id": 2
				 *                      }
				 *                 ),
				 *                 @OA\Examples(
				 *                      summary="Adding error",
				 *               		value={
				 *                          "error": "Error add call request", "data": { "message": "description of error" }
				 *                      }
				 *                 )
				 *             }
				 *         )
				 *     )
				 * )
				 * */
	
				$params = json_decode(file_get_contents('php://input'), true);

				$id_point = $mysqli -> real_escape_string($params['id_point']);
				$coordinates = $mysqli -> real_escape_string($params['coordinates']);
				$comment = $mysqli -> real_escape_string($params['comment']);
				$created_at = $mysqli -> real_escape_string($params['created_at']);
				$invite = $mysqli -> real_escape_string($params['invite']);
				$name = $mysqli -> real_escape_string($params['name']);
				$team = $mysqli -> real_escape_string($params['team']);

				if (empty($invite)) {
					$file = __DIR__.'/sql/addPointReport.sql';
					$sql = getSql(
						$file,
						array(
							'id_user' => $uid,
							'id_point' => $id_point,
							'coordinates' => $coordinates,
							'comment' => $comment,
							'created_at' => $created_at
						)
					);
				} else {
					$id_user = 'NULL';
					$id_event = 'NULL';
					$id_event_member = 'NULL';
					$nameParts = explode(' ', $name);
					$q = $mysqli -> query("SELECT id, id_user, id_event, name, surname FROM event_members WHERE token='{$invite}' LIMIT 1");
					if ($q && $q->num_rows == 1) {
						$old = $q -> fetch_assoc();

						if (!empty($old['id_user'])) {
							$id_user = $old['id_user'];
						}
						if (!empty($old['id_event'])) {
							$id_event = $old['id_event'];
						}
						
						if (!empty($old['id'])) {
							$id_event_member = $old['id'];
						}
						$patch = array();

						$patch['surname'] = !empty($nameParts[0]) ? trim($nameParts[0]) : $old['surname'];
						$patch['name'] = !empty($nameParts[1]) ? trim($nameParts[1]) : $old['name'];
						$patch['team'] = !empty($team) ? trim($team) : $old['team'];
						$z = "UPDATE event_members SET ";
						foreach($patch as $k => $v) {
							$z .= "`{$k}` = '{$v}',";
						}

						$z .= " id=".$old['id']." WHERE id=".$old['id'];
						$q = $mysqli->query($z);
						if (!$q) { die(err('Error add point report', array('message' => $mysqli->error, 'z' => $z))); }
					}

					$file = __DIR__.'/sql/addPointReportWithInvite.sql';
					
					$sql = getSql(
						$file,
						array(
							'id_user' => $id_user,
							'id_author' => $uid,
							'id_point' => $id_point,
							'id_event' => $id_event,
							'id_event_member' => $id_event_member,
							'coordinates' => $coordinates,
							'comment' => $name.'|'.$comment,
							'created_at' => $created_at
						)
					);
				}
				
				$q = $mysqli->query($sql);
				if (!$q) { die(err('Error add point report', array('message' => $mysqli->error, 'sql' => $sql, 'file'=>$file))); }
	
				exit(jout(array('success' => true, 'id' => $mysqli->insert_id)));
		
					break;
        default:
            die(err('Unsupported Method', $_SERVER['REQUEST_METHOD']));
    }
