<?php

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
		die('wrong cookieData');
	}

	include_once('db.php');
	
	$sql = "SELECT
		id_user, token
		FROM user_tokens
		WHERE id_user='".$cookieData['id']."' AND token='".$cookieData['token']."'
		LIMIT 1";
	$q = $mysqli->query($sql);
	if (!$q) {
		die('Error reading user login info/ '. $mysqli->error);
	}

	if ($q->num_rows === 0) {
		
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
				$url = "https://";   
		else  
				$url = "http://";   
		// Append the host(domain name, ip) to the URL.   
		$url.= $_SERVER['HTTP_HOST'];  
		
		$dom = $url;
		
		// Append the requested resource location to the URL   
		$url.= $_SERVER['REQUEST_URI'];    
			
		$redir = $dom."/oauth/?redir=".$url;
		header("Location: https://www.strava.com/oauth/authorize?client_id=73436&response_type=code&approval_prompt=auto&scope=activity:read&redirect_uri=".$redir);
		exit();
		return die('bad token or user');
	}
	
	$r = $q -> fetch_row();
	
	$uid = $r[0];
	