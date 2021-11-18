<?php
	include_once(__DIR__.'/../api/auth/_includes/checkAuth.php');
	
	$checkAuthResult = checkAuth();
	
	if($checkAuthResult['success'] !== true) {
		die(jout($checkAuthResult));
	}
	
	$uid = $checkAuthResult['user_id'];