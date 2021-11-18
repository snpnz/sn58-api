<?php
if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/_config.php')){
    die("need _config.php");
}
$config = include_once($_SERVER['DOCUMENT_ROOT'].'/_config.php');

function getConf($key) {
    global $config;
    return $config[$key];
}