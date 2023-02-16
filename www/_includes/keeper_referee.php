<?php

function goauth(){
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
header("Location: https://pohodnik.tk/login?client_id=1&redirect_uri=".$redir);
}

	if (isset($_GET['token']) && isset($_GET['expiration']) && isset($_GET['id'])) {
		$cookie = json_encode(array(
			"token" => $_GET['token'],
			"expiration" => $_GET['expiration'],
			"id" => $_GET['id']
		));
	} else {
		$cookie = isset($_SERVER['HTTP_AUTHORIZATION']) && !empty($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : $_COOKIE["snpnz-auth"];

	}


	if (!isset($cookie) || empty($cookie)) {
		goauth();
		die('wrong cookie');
	}

	$cookieData = json_decode($cookie, true);

	if (!isset($cookieData) || !isset($cookieData['token'])) {
		goauth();
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
		goauth();
		exit();
		return die('bad token or user');
	}

	$r = $q -> fetch_row();

	$uid = $r[0];


	$sql = "SELECT deadline FROM referees WHERE id_user={$uid} AND deadline >= NOW() AND created_at <= NOW() LIMIT 1";
	$q = $mysqli->query($sql);
	if (!$q) {
		die('Error reading user info/ '. $mysqli->error);
	}
	$res = $q -> fetch_assoc();

	if($q->num_rows == 0) {
		die('<div class="alert alert-dark m-5" role="alert">
		('.$uid.') You are not referee...
	  </div>');
	}

	?>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="/admin/referee/">Sn58 referee</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarText">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="/admin/referee/">Точки</a>
        </li>
		<li class="nav-item">
          <a class="nav-link" aria-current="page" href="/admin/referee/print.php">Группы точек (маршруты)</a>
        </li>
      </ul>
      <span class="navbar-text">
	  <?=$res['name']?> <?=$res['surname']?>
      </span>
    </div>
  </div>
</nav>
