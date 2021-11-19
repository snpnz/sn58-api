<?php
	include_once(__DIR__.'/../api/auth/_includes/checkAuth.php');
	include_once('resp.php');
	
	$checkAuthResult = checkAuth();
	
	if($checkAuthResult['success'] !== true) {
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
	}
	
	$uid = $checkAuthResult['user_id'];
	