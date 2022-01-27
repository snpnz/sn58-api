<?php
    if (!isset($_GET['code'])) {
        die('Wrong code');
    }
    
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
    $user_id = 0;

    $z = "SELECT id FROM users WHERE strava_id='{$strava_id}' LIMIT 1";
    $q = $mysqli -> query($z);
    if (!$q) { die($mysqli -> error); }
    
    if ($q -> num_rows == 1) {
        $r = $q -> fetch_row();
        $user_id = $r[0];
    }

    if (iseet($_GET['invite'])) {
        $invite = $mysqli -> real_escape_string($_GET['invite']);
        $z = "SELECT id_user FROM user_tokens WHERE token='{$invite}' LIMIT 1";
        $q = $mysqli -> query($z);
        if (!$q) { die('find token by invite code '.$mysqli -> error); }

        if ($q -> num_rows == 1) {
            $r = $q -> fetch_row();
            $user_id = $r[0];

            $z = "UPDATE users SET
                `login` = '".$res['athlete']['username']."',
                `name` = '".$res['athlete']['firstname']."',
                `surname` = '".$res['athlete']['lastname']."',
                `photo` = '".$res['athlete']['profile']."',
                `strava_id` = '".$res['athlete']['id']."'
            WHERE id={$user_id}";
            $q = $mysqli -> query($z);
            if (!$q) { die("update profile by strava data ".$mysqli -> error); }
        }
    }


    

    if (!($user_id > 0)) {
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
    } 
    

    $z = "SELECT id FROM user_tokens WHERE token='".$res['access_token']."' LIMIT 1";
    $q = $mysqli -> query($z);
    if (!$q) { die($mysqli -> error); }

        if ($q->num_rows === 0) {
            $ip = getIp();
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