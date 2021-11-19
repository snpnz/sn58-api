<?php

	function checkAuth() {

	
		if (isset($_GET['token']) && isset($_GET['expiration']) && isset($_GET['id'])) {
			$cookie = json_encode(array(
				"token" => $_GET['token'],
				"expiration" => $_GET['expiration'],
				"id" => $_GET['id']
			));

			print_r($cookie);
			die();
		} else {
			$cookie = isset($_SERVER['HTTP_AUTHORIZATION']) && !empty($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : $_COOKIE["snpnz-auth"];

		}

		
		if (!isset($cookie) || empty($cookie)) {
			return array('success' => false, 'reason' => 'wrong cookie');
		}
		
		$cookieData = json_decode($cookie, true);
		
		if (!isset($cookieData) || !isset($cookieData['token'])) {
			return array('success' => false, 'reason' => 'wrong cookieData', 'data' => $cookieData);
		}

		include_once($_SERVER['DOCUMENT_ROOT'].'/_includes/db.php');
		global $mysqli;
		
		$sql = "SELECT
			id_user, token
			FROM user_tokens
			WHERE id_user='".$cookieData['id']."' AND token='".$cookieData['token']."'
			LIMIT 1";
		$q = $mysqli->query($sql);
		if (!$q) {
			die(err('Error reading user login info', array('message' => $mysqli->error, 'sql' => $sql)));
		}

		if ($q->num_rows === 0) {
			return array('success' => false, 'reason' => 'bad token or user');
		}
		
		$r = $q -> fetch_row();
		
		return array('success' => true, 'user_id' => $r[0]);
	}
	