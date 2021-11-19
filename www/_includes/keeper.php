<?php
	include_once(__DIR__.'/../api/auth/_includes/checkAuth.php');
	include_once('resp.php');
	
	$checkAuthResult = checkAuth();
	
	if($checkAuthResult['success'] !== true) {
		die(jout($checkAuthResult));
	}
	
	$uid = $checkAuthResult['user_id'];