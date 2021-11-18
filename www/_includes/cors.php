<?php
header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN'] . "");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
?>