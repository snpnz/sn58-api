<?php 
require_once('cors.php');
require_once('config.php');

$host = getConf('MYSQL_HOST');
$user = getConf('MYSQL_USER');
$psw = getConf('MYSQL_PASSWORD');
$db = getConf('MYSQL_DATABASE');

require_once('resp.php');

$mysqli = mysqli_connect($host, $user, $psw, $db);
if (!$mysqli) {	
    exit(err("Connection failed to $db on $host", mysqli_connect_error()));
}