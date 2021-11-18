<?php
error_reporting(E_ALL);
require_once('../_includes/config.php');

if (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] != getConf('MIGRATOR_USER') || $_SERVER['PHP_AUTH_PW'] != getConf('MIGRATOR_PASSWORD')) {
    header('WWW-Authenticate: Basic realm="SnPnz Migrator"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Без авторизации тут делать нечего';
    exit;
}

define("MIGRATIONS_FOLDER", __DIR__."/migrations");
define("MIGRATIONS_DATA_FOLDER", __DIR__."/testData");

$host = getConf('MYSQL_HOST');
$user = getConf('MYSQL_USER');
$psw = getConf('MYSQL_PASSWORD');
$db = getConf('MYSQL_DATABASE');

// FROMAT YYYY-MM-DD-HH-MM


$mysqli = mysqli_connect($host, $user, $psw, $db);
if (!$mysqli) {
    exit("Connection failed to $db on $host". mysqli_connect_error());
}


$q = $mysqli->query("SELECT count(*) FROM information_schema.TABLES WHERE TABLE_NAME = 'migrations' AND TABLE_SCHEMA in (SELECT DATABASE());");
if(!$q) { die("Ошибка проверки существания таблицы миграций. ".$mysqli->error); }
$res = $q->fetch_row();
$needInit = intval($res[0]) === 0;

$finishedMigrations = array();

if ($needInit) {
	$q = $mysqli->query("CREATE TABLE `migrations`
	( `id` INT(5) NOT NULL AUTO_INCREMENT , `date` DATETIME NOT NULL , `name` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`))
	ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci COMMENT = 'Миграции'");
	
	if(!$q) { die("Ошибка создания таблицы миграций. ".$mysqli->error); }
} else {
	$q = $mysqli->query("SELECT name, date FROM migrations ORDER BY date DESC");
	if(!$q) { die("Ошибка проверки прогнанных миграций. ".$mysqli->error); }
	while($res = $q->fetch_assoc()) {
		$finishedMigrations[strtolower($res['name'])] = $res['date'];
	}
}

$folder = isset($_GET['data']) ? MIGRATIONS_DATA_FOLDER : MIGRATIONS_FOLDER;
$contents = scandir($folder, SCANDIR_SORT_ASCENDING);
	echo "<style>* {font-family: monospace}</style>";
    echo "<table border=0>";
    foreach ( $contents as $item ) {
		if (strrpos($item, ".")===0 || strrpos($item, "/")===0) {
			continue;
		}
		
		$alreadyFinished = isset($finishedMigrations[strtolower($item)]) ? $finishedMigrations[strtolower($item)] : NULL;
		
		if (!empty($alreadyFinished)) {
			echo "<tr><td>$item</td><td>🚀 <span style='color:orange'>Уже прогоняли $alreadyFinished<span></td></tr>";
			continue;
		}
        if ( !is_dir($item)) {
            echo "<tr><td>$item</td><td>";
			$commands = file_get_contents($folder."/".$item);
			$q = $mysqli->multi_query($commands);
			

			
			if($q) {
				
				do {
					if ($res = $mysqli->store_result()) {
						$res->free();
					}
				} while ($mysqli->more_results() && $mysqli->next_result()); 



				$q = $mysqli->query("INSERT INTO `migrations`(`date`, `name`) VALUES (NOW(),'$item')");
				$err = $mysqli->error;
				echo($err ? "<p>Ошибка логгирования миграции $item. ".$err."</p>" :"✅ <b style='color:green'>Прогнали успешно</b>" );
	
			} else {
				echo("⛔ <b style='color:red'>Ошибка прогона миграции $item. ".$mysqli->error."</b>");
			}
			echo "</td></tr>";
        }
    }
    echo "</table>";
 ?>
