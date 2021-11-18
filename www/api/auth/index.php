<?php
	require_once('../../_includes/resp.php');
	require_once('../../_includes/db.php');
	require_once('../../_includes/ip.php');
	require_once('./_includes/checkAuth.php');

	switch($_SERVER['REQUEST_METHOD'])
	{
		case 'GET':
		/**
		 * @OA\Get(
		 *     tags={"Auth"},
		 *     path="/api/auth/",
		 *     summary="Check if current user is logged in",
		 *     @OA\Response(
		 *         response="200",
		 *         description="Response with ok|fail result",
		 *                 @OA\JsonContent(
		 *                		examples={
         *                         @OA\Examples(
         *                              summary="Response with ok result",
         *                        		value={
         *                                 "success": true, "user_id": 2
         *                              }
         *                         ),
         *                         @OA\Examples(
         *                               summary="Response for empty cookie",
         *                         		 value={
         *                                  "success": false, "reason": "wrong cookie"
         *                               }
         *                         ),
         *                         @OA\Examples(
         *                              summary="Response for bad token",
         *                       		value={
         *                                 "success": false, "reason": "bad token or user"
         *                              }
         *                         ),
         *                         @OA\Examples(
         *                              summary="Response for not isset token",
         *                       		value={
         *                                 "success": false, "reason": "wrong cookieData", "data":"object data"
         *                              }
         *                         ),
		 *                     }
		 *                 )
		 *     )
		 * )
		 */
			die(jout(checkAuth()));

			break;
		case 'POST':
		/**
		 * @OA\Post(
		 *     tags={"Auth"},
		 *     path="/api/auth/",
		 *     summary="Login user by login and password",
		 *     @OA\RequestBody(
		 *         @OA\MediaType(
		 *             mediaType="application/json",
		 *             @OA\Schema(
		 *                 example={"login": "ugoljok", "password": "1111"}
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
			$login = $mysqli->real_escape_string(trim(strtolower($params['login'])));
			$password = $mysqli->real_escape_string(trim($params['password']));

			if (empty($login)) {
				die(err('Login is required', $login));
			}

			if (empty($password)) {
				die(err('Password is required', $password));
			}

			$sql = "SELECT id, password FROM users WHERE login='$login' LIMIT 1";
			$q = $mysqli->query($sql);
			if (!$q) {
				die(err('Error reading user info by login', array('message' => $mysqli->error, 'sql' => $sql)));
			}

			if ($q->num_rows === 0) {
				die(err('Login is not exist', $login));
			}

			$user = $q -> fetch_assoc();

			if ($user['password'] !== md5(md5($password))) {
				die(err('Incorrect password', md5(md5($password))));
			}

			$ip = getIp();
			$token = md5("snpnz-auth".time().$user['id'].$ip);

			$sql = "INSERT INTO user_tokens SET id_user=".$user['id'].", token='$token', created_at=NOW(), ip=".ip2long($ip);
			$q = $mysqli->query($sql);
			if (!$q) {
				die(err('Failed update login data', array('message' => $mysqli->error, 'sql' => $sql)));
			}

			$exp = time() + (86400 * 90);

			$authdata = array(
				'id' => $user['id'],
				'token' => $token,
				'expiration' => $exp
			);

			setcookie("snpnz-auth", json_encode($authdata), $exp, "/", $_SERVER['HTTP_HOST']);

			exit(jout($authdata));

			break;


		case 'DELETE':
		/**
		 * @OA\Delete(
         *     tags={"Auth"},
		 *     path="/api/auth/",
		 *     summary="Logout user",
		 *     @OA\Response(
         *         response="200",
         *         description="Response with ok|fail result",
         *                 @OA\JsonContent(
         *                		examples={
         *                         @OA\Examples(
         *                              summary="Response with ok result",
         *                        		value={
         *                                 "success":true
         *                              }
         *                         ),
         *                         @OA\Examples(
         *                               summary="No user is authorized",
         *                         		 value={
         *                                  "error":"No user is authorized","data":null
         *                               }
         *                         ),
         *                     }
         *                 )
         *     )
		 * )
		 */
			$cookie = $_COOKIE["snpnz-auth"];
            if (!isset($cookie) || empty($cookie)) {
                die(err('No user is authorized'));
            }
			setcookie("snpnz-auth", "", time() - 3600, "/", $_SERVER['HTTP_HOST']);
			exit(jout(array("success" => true)));

			break;
		default:
		die(err('Unsupported Method', $_SERVER['REQUEST_METHOD']));
	}
