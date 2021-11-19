<?php
    if (!isset($_GET['code'])) {
        die('Wrong code');
    }
 //print_r($_GET);
 //Array ( [redir] => http://localhost:3000 [state] => [code] => 4225a8733344ee1bfde968832682bd044d9a3773 [scope] => read,activity:read )
	require_once('../_includes/db.php');
    require_once('../_includes/ip.php');

    $client_id = '73436';
    $client_secret = 'ba9fb913d81fc6941fe0d6e96011de332fff2697';

    $post_data="client_id={$client_id}&client_secret={$client_secret}&grant_type=authorization_code&code=".$_GET['code'];
    $url="https://www.strava.com/api/v3/oauth/token";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));   
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    $result = curl_exec($ch);
    
    $res = json_decode($result, true);
    
    if(!isset($res['access_token'])) {
        die('wrong read data');
    }

    $strava_id = $res['athlete']['id'];

    $z = "SELECT id FROM users WHERE strava_id='{$strava_id}' LIMIT 1";
    $q = $mysqli -> query($z);
    if (!$q) { die($mysqli -> error); }


    $ip = getIp();

    if ($q->num_rows === 0) {
        $z = "
        INSERT INTO `users`(
            `login`,
            `name`,
            `surname`,
            `photo`,
            `password`,
            `strava_id`,
            `register_date`
        )
        VALUES(
            '".$res['athlete']['username']."',
            '".$res['athlete']['firstname']."',
            '".$res['athlete']['lastname']."',
            '".$res['athlete']['profile']."',
            '',
            '".$res['athlete']['id']."',
            NOW()
        )";
        $q = $mysqli -> query($z);
        if (!$q) { die($mysqli -> error); }
        $user_id = $mysqli -> insert_id;
    } else {
        $r = $q -> fetch_assoc();
        $user_id = $r['id'];

        $z = "SELECT id FROM user_tokens WHERE token='".$res['access_token']."' LIMIT 1";
        $q = $mysqli -> query($z);
        if (!$q) { die($mysqli -> error); }

        if ($q->num_rows === 0) {
        
            $z = "
            INSERT INTO `user_tokens`(
                `id_user`,
                `token`,
                `refresh_token`,
                `created_at`,
                `expires_at`,
                `ip`
            )
            VALUES(
                '".$user_id."',
                '".$res['access_token']."',
                '".$res['refresh_token']."',
                NOW(),
                '".date('Y-m-d H:i:s', $res['expires_at'])."',
                ".ip2long($ip)."
            )";
            $q = $mysqli -> query($z);
            if (!$q) { die($mysqli -> error); }
        }

    }


        $authdata = array(
            'id' => $user_id,
            'token' => $res['access_token'],
            'expiration' => $res['expires_at']
        );

        setcookie("snpnz-auth", json_encode($authdata), $res['expires_at'], "/", $_SERVER['HTTP_HOST']);
        setcookie("snpnz-auth", json_encode($authdata), $res['expires_at'], '/', $_SERVER['HTTP_HOST']);
        setcookie("snpnz-auth", json_encode($authdata), $res['expires_at'], '/', 'localhost:3000');
        setcookie("snpnz-auth", json_encode($authdata), $res['expires_at'], '/', 'localhost');

        header('Location: '.$_GET['redir']."?token=".$res['access_token']."&expiration=".$res['expires_at']."&id=".$user_id);