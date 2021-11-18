<?php

	function checkAuth() {
		$cookie = isset($_GET['token']) ? $_GET['token'] : $_COOKIE["snpnz-auth"];
		if (!isset($cookie) || empty($cookie)) {
			return array('success' => false, 'reason' => 'wrong cookie');
		}
		
		$cookieData = json_decode($cookie, true);
		
		if (!isset($cookieData) || !isset($cookieData['token'])) {
			return array('success' => false, 'reason' => 'wrong cookieData', 'data' => $cookieData);
		}
		require($_SERVER['DOCUMENT_ROOT'].'/_includes/db.php');
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
	