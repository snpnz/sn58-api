<?php
    if (!isset($_GET['token'])) {
        die('Wrong token');
    }

	require_once('../_includes/db.php');
    require_once('../_includes/ip.php');

    $client_id = '1';
    $client_secret = 'vidW8m4Wulegb6EPy5qihpj1DlW7kpi';

    $post_data="client={$client_id}&secret={$client_secret}&token=".$_GET['token'];
    $url="https://pohodnik.tk/ajax/externalApp/";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);

    $res = json_decode($result, true);



    if(!isset($res['id'])) {
        die('wrong read data');
    }
    $pohodnik_id = $res['id'];

    $z = "SELECT id FROM users WHERE strava_id='{$pohodnik_id}' LIMIT 1";
    $q = $mysqli -> query($z);
    if (!$q) { die($mysqli -> error); }

    if ($q -> num_rows == 1) {
        $r = $q -> fetch_row();
        $user_id = $r[0];
    }

    if (isset($_GET['invite'])) {
        $invite = $mysqli -> real_escape_string($_GET['invite']);
        $z = "SELECT id_user FROM user_tokens WHERE token='{$invite}' LIMIT 1";
        $q = $mysqli -> query($z);
        if (!$q) { die('find token by invite code '.$mysqli -> error); }

        if ($q -> num_rows == 1) {
            $r = $q -> fetch_row();
            $user_id = $r[0];

            $z = "UPDATE users SET
                `login` = '".(isset($res['email']) ? $res['email'] : 'pohodnik_'.$res['id'])."',
                `name` = '".$res['name']."',
                `surname` = '".$res['surname']."',
                `photo` = '".$res['photo_50']."',
                `strava_id` = '{$pohodnik_id}'
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
            '".(isset($res['email'])?$res['email']:'pohodnik_'.$pohodnik_id)."',
            '".$res['name']."',
            '".$res['surname']."',
            '".$res['photo_50']."',
            '',
            '".$res['id']."',
            NOW()
        )";
        $q = $mysqli -> query($z);
        if (!$q) { die($mysqli -> error); }
        $user_id = $mysqli -> insert_id;
    }


    $z = "SELECT id FROM user_tokens WHERE token='".$_GET['token']."' LIMIT 1";
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
                '".$_GET['token']."',
                '".$_GET['token']."',
                NOW(),
                '".date('Y-m-d H:i:s', time() + (86400 * 30)."',
                ".ip2long($ip)."
            )";
            $q = $mysqli -> query($z);
            if (!$q) { die($mysqli -> error); }
        }




        $authdata = array(
            'id' => $user_id,
            'token' => $_GET['token'],
            'expiration' => time() + (86400 * 30)
        );

        setcookie("snpnz-auth", json_encode($authdata), $res['expires_at'], "/", $_SERVER['HTTP_HOST']);
        setcookie("snpnz-auth", json_encode($authdata), $res['expires_at'], '/', $_SERVER['HTTP_HOST']);
        setcookie("snpnz-auth", json_encode($authdata), $res['expires_at'], '/', 'localhost:3000');
        setcookie("snpnz-auth", json_encode($authdata), $res['expires_at'], '/', 'localhost');

        header('Location: '.$_GET['redir']."?token=".$res['access_token']."&expiration=".$res['expires_at']."&id=".$user_id);
