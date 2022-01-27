<?php
	require_once('../../_includes/resp.php');
	require_once('../../_includes/db.php');
	require_once('../../_includes/ip.php');

	switch($_SERVER['REQUEST_METHOD'])
	{
		case 'POST':
		/**
		 * @OA\Post(
		 *     tags={"Register"},
		 *     path="/api/register/",
		 *     summary="Register user",
		 *     @OA\RequestBody(
		 *         @OA\MediaType(
		 *             mediaType="application/json",
		 *             @OA\Schema(
		 *                 example={
		 *					"login": "ololosha89",
    	 *					"name": "Ололоша",
    	 *					"surname": "Ололоев",
    	 *					"photo": "http://images.com/photo123.jpg",
    	 *					"password": "12345",
    	 *					"strava_id": "12345",
		 *                  "invite": "aDSc5DEFcFD1B45cAaa",
		 * 				   }
		 *             )
		 *         )
		 *     ),
		 *     @OA\Response(
         *         response="200",
         *         description="Response with ok|fail result",
         *                 @OA\JsonContent(
         *                		examples={
         *                         @OA\Examples(
         *                              summary="Response with ok result",
         *                        		value={
         *                                 "id":"3","token":"81150753845bbc950b7d67838cc1179a","expiration":1614368976
         *                              }
         *                         ),
         *                         @OA\Examples(
         *                               summary="Login is required",
         *                         		 value={
         *                                  "error":"Login is required","data":""
         *                               }
         *                         ),
         *                         @OA\Examples(
         *                              summary="Password is required",
         *                       		value={
         *                                 "error":"Password is required","data":""
         *                              }
         *                         ),
         *                         @OA\Examples(
         *                              summary="Login is not exist",
         *                       		value={
         *                                 "error":"Login is not exist","data":"ugoljok444"
         *                              }
         *                         ),
         *                         @OA\Examples(
         *                              summary="Incorrect password",
         *                       		value={
         *                                 "error":"Incorrect password","data":"79f322092d81ca9e50b01f2485ed0a97"
         *                              }
         *                         ),
         *                     }
         *                 )
         *     )
		 * )
		 */

			$params = json_decode(file_get_contents('php://input'), true);
	
			$login = $mysqli->real_escape_string(trim($params['login']));
			$name = $mysqli->real_escape_string(trim($params['name']));
			$surname = $mysqli->real_escape_string(trim($params['surname']));
			$photo = $mysqli->real_escape_string(trim($params['photo']));
			$password = $mysqli->real_escape_string(trim($params['password']));
			$strava_id = $mysqli->real_escape_string(trim($params['strava_id']));
			$invite = $mysqli->real_escape_string(trim($params['invite']));

			if (empty($login)) {
				die(err('Login is required', $login));
			}

			if (empty($name)) {
				die(err('Login is required', $login));
			}


			if (empty($surname)) {
				die(err('Login is required', $login));
			}

			if (empty($password)) {
				die(err('Password is required', $password));
			}

			$sql = "SELECT id, strava_id, `login` FROM users WHERE login='$login' OR strava_id='$strava_id' LIMIT 1";
			$q = $mysqli->query($sql);
			if (!$q) {
				die(err('Error reading user duplictes', array('message' => $mysqli->error, 'sql' => $sql)));
			}

			if ($q->num_rows === 1) {
				die(err('Already Exist', $q -> fetch_assoc()));
			}

			$psw = md5(md5($password));
			$sql = "INSERT INTO users SET
				`login`='{$login}',
				`name`='{$name}',
				`surname`='{$surname}',
				`photo`='{$photo}',
				`password`='{$psw}',
				`strava_id`='{$strava_id}',
				`register_date`=NOW()
			";
			$q = $mysqli->query($sql);
			if (!$q) {
				die(err('Error reading user duplictes', array('message' => $mysqli->error, 'sql' => $sql)));
			}

			$id_user = $mysqli -> insert_id;

			$ip = getIp();
			$token = md5("snpnz-auth".time().$id_user.$ip);
			$exp = time() + (86400 * 90);
			$sql = "INSERT INTO user_tokens SET
			id_user='{$id_user}', token='$token', created_at=NOW(), expires_at='".date('Y-m-d H:i:s', $exp)."', ip=".ip2long($ip);
			$q = $mysqli->query($sql);
			if (!$q) {
				die(err('Failed update tokens data', array('message' => $mysqli->error, 'sql' => $sql)));
			}



			$authdata = array(
				'id' => $id_user,
				'token' => $token,
				'expiration' => $exp
			);

			setcookie("snpnz-auth", json_encode($authdata), $exp, "/", $_SERVER['HTTP_HOST']);

			exit(jout($authdata));

			break;

		default:
		die(err('Unsupported Method', $_SERVER['REQUEST_METHOD']));
	}
