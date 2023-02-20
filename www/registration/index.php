<?php

      if (isset($_GET['invite'])) {
        include_once('../_includes/db.php');
        $invite = $mysqli->real_escape_string($_GET['invite']);
        $q = $mysqli->query("SELECT id_event FROM event_members WHERE token='{$invite}' LIMIT 1");
        if (!$q) { die($mysqli->error); }
        if ($q -> num_rows == 1) {
          $r = $q->fetch_row();
          $id_event = $r[0];
        } else {
          die('wrong invite '.$invite);
        }
      } else {
        $id_event = intval($_GET['event']);
      }

      if (!($id_event)) {
        die('Это какое-то неведомое событие...');
      }
      include_once('../_includes/db.php');
      $q = $mysqli->query("SELECT events.* FROM events WHERE id=".$id_event);
      if(!$q) { die($mysqli->error);}
      $event = $q -> fetch_assoc();

      $url = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
      $url.= $_SERVER['HTTP_HOST'];
      $dom = $url;
      $url.= $_SERVER['REQUEST_URI'];
      $redir = $dom."/oauth/?redir=".$url;

      $link = $dom.'/registration?event='.$id_event;

    ?>
<!doctype html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">



    <meta property="og:title" content="<?=$event['name']?>">
    <meta property="og:type" content="article" />
    <meta property="og:image" content="https://sn.fednik.ru/android-chrome-192x192.png">
    <meta property="og:url" content="<?=$link?>">
    <meta name="twitter:card" content="https://sn.fednik.ru/android-chrome-192x192.png">

    <!--  Non-Essential, But Recommended -->
    <meta property="og:description" content="<?=$event['description']?>">
    <meta property="og:site_name" content="<?=$event['name']?>">
    <meta name="twitter:image:alt" content="<?=$event['name']?>">

    <!--  Non-Essential, But Required for Analytics -->
    <title><?=$event['name']?></title>
    <style>

    .divider {
      position: relative;
      min-height:30px;
    }

    .divider:before {
      left:0;
      top:15px;
      content: '';
      position: absolute;
      border-bottom:2px solid #adadad;
      height: 0;
      width: 100%;
    }

    .divider span {
      position: absolute;
      left:50%; top: 50%;
      transform: translate(-50%, -50%);
      background: #fff;
      padding: 0 10px;
    }

    </style>
  </head>
  <body>

  <div class="px-4 py-5 my-5 text-center">
    <h1 class="display-5 fw-bold"><?=$event['name']?></h1>
    <div class="col-lg-6 mx-auto">
      <p class="lead"><?=$event['description']?></p>
      <p class="lead" title="завершение <?=date('d.m.Y H:i', strtotime($event['finish_at']))?>">начало: <?=date('d.m.Y в H:i', strtotime($event['start_at']))?></p>

      <?php


        $displayError = "";
        $displaySuccess = "";
        $displayWarns = "";
        $already = "";

        // Register form send data

        if (isset($_POST['name']) && isset(($_POST['surname'])) && isset(($_POST['email']))) {

          $email = $mysqli->real_escape_string(trim($_POST['email']));
          $name = $mysqli->real_escape_string(trim($_POST['name']));
          $surname = $mysqli->real_escape_string(trim($_POST['surname']));

          $q = $mysqli->query("SELECT id FROM users WHERE login='{$email}' LIMIT 1");
          if(!$q) {
            $displayError = $mysqli->error;
          } else if ($q -> num_rows == 1){
            $curUser = $q -> fetch_assoc();
          }


            $q = $mysqli->query("
            SELECT id, name, token, created_at FROM event_members
            WHERE ".(isset($curUser) ? 'id_user='.$curUser['id'] : "login='{$email}'")."
            AND id_event={$id_event} LIMIT 1 ");
            if(!$q) {
              $displayError = $mysqli->error;
            } else if ($q->num_rows == 1){
              $rr = $q -> fetch_assoc();
              $already = $rr['token'];
              $displayWarns = $rr['name'].", Вы успешно записались на это событие ".date('d.m.y в H:i', strtotime($rr['created_at']));
            }


            if (empty($displayError) && empty($displayWarns)) {
              $token = md5("snpnz-invite".time().$email);
              $q = $mysqli->query("
                INSERT INTO
                    `event_members`
                SET
                    `id_event` = {$id_event},
                    `created_at` = NOW(),
                    `id_author` = ".(isset($curUser) ? $curUser['id'] : 'NULL').",
                    `id_user` = ".(isset($curUser) ? $curUser['id'] : 'NULL').",
                    `name` = '{$name}',
                    `surname` = '{$surname}',
                    `login` = '{$email}',
                    `token` = '{$token}',
                    `accepted_at` = ".(isset($curUser) ? 'NOW()' : 'NULL')."
              ");
              if(!$q) {
                $displayError = $mysqli->error;
              } else {

                $to      = $email;
                $subject = 'Подтверждение участия в событии "'.$event['name'].'"';
                $message = '<p>Здравствуйте, '.$name.'<p>';
                $message .= '<p>Для подтверждения участия в мероприятии <b>'.$event['name'].'</b>';
                $link = $dom.'/registration?event='.$id_event.'&token='.$token;
                $message .= ' - перейдите по ссылке <a href="'.$link.'">'.$link.'</a><p>';
                $headers = "Content-Type: text/html; charset=UTF-8\r\n";

                mail($to, $subject, $message, $headers);

                $displaySuccess = "Для подтверждения нужно перейти по ссылке из письма отправленного на адрес {$email} <small>(если не нашли письмо, проверьте Спам)</small>";
              }
            }
        }
        // end Register form send data
      ?>



<?php
  if (isset($_GET['token'])) {
    $token = $mysqli -> real_escape_string($_GET['token']);
    $q = $mysqli->query("SELECT
      `id`, `id_event`, `created_at`, `id_author`, `id_user`, `name`, `surname`, `login`, `token`, `accepted_at`
      FROM event_members WHERE token='{$token}' LIMIT 1");
    if(!$q) {
      $displayError = $mysqli->error;
    } else if ($q->num_rows == 1) {
      $re = $q-> fetch_assoc();
      $q = $mysqli->query("SELECT id FROM users WHERE login='".$re['login']."' LIMIT 1");
      if ($q && $q-> num_rows == 1) {
        $res = $q->fetch_row();
        $uid = $res[0];
      } else {
        $z = "
        INSERT INTO
            `users`
        SET
            `login` = '".$re['login']."',
            `name` = '".$re['name']."',
            `surname` = '".$re['surname']."',
            `photo` = '',
            `register_date` = NOW()
        ";
        $q = $mysqli->query($z);
        if(!$q) {
          $displayError = $mysqli->error;
        } else {
          $uid = $mysqli -> insert_id;
        }
      }

      if (!empty($uid)) {
          $q = $mysqli->query("SELECT id, name, accepted_at FROM event_members
          WHERE id_user={$uid} AND token='{$token}' AND accepted_at IS NOT NULL LIMIT 1");
          if(!$q) {
            $displayError = $mysqli->error;
          } else if ($q && $q -> num_rows == 1) {
            $r = $q->fetch_assoc();
            $already = $token;
            $displaySuccess = $r['name'].', Вы успешно подтвердили свое участие '.date('d.m.y в H:i', strtotime($r['accepted_at']));
          } else {
            $q = $mysqli->query("UPDATE event_members SET id_user={$uid}, accepted_at=NOW() WHERE token='{$token}'");
            if(!$q) {
              $displayError = $mysqli->error;
            } else {

              $displaySuccess = "Вы успешно подтвердили свое участие.";
              $already = $token;
            }
          }

      }
    }
  }
?>


<?php
        if (isset($_COOKIE['snpnz-auth'])) {
          $data = json_decode($_COOKIE['snpnz-auth'], true);
          $token = $data['token'];

          $q = $mysqli->query("
            SELECT
              users.id, users.login, users.name, users.surname
            FROM
              users
              LEFT JOIN user_tokens ON user_tokens.id_user = users.id
            WHERE user_tokens.token='{$token}'");
          if(!$q) {
            $displayError = $mysqli->error;
          } else if ($q->num_rows ==0) {
            $displayError = "Ошибка авторизации ".$token;
          } else {
            $curUser = $q -> fetch_assoc();
          }


            $q = $mysqli->query("
            SELECT id, created_at, token FROM event_members
            WHERE id_user='".$curUser['id']."'"."
            AND id_event={$id_event} LIMIT 1 ");
            if(!$q) {
              $displayError = $mysqli->error;
            } else if ($q->num_rows == 1){
              $rr = $q -> fetch_assoc();
              $already = $rr['token'];
              $displayWarns = $curUser['name'].", Вы успешно записались на это событие ".date('d.m.y в H:i', strtotime($rr['created_at']));
            }


            if (empty($displayError) && empty($displayWarns)) {


              $token = md5("snpnz-invite".time().$curUser['id']);

              $q = $mysqli->query("
                INSERT INTO
                    `event_members`
                SET
                    `id_event` = {$id_event},
                    `created_at` = NOW(),
                    `id_author` = ".$curUser['id'].",
                    `id_user` = ".$curUser['id'].",
                    `name` = '".$curUser['name']."',
                    `surname` = '".$curUser['surname']."',
                    `login` = '".$curUser['email']."',
                    `token` = '".$token."',
                    `accepted_at` = NOW()
              ");
              if(!$q) {
                $displayError = $mysqli->error;
              } else {
                $displaySuccess = "Вы успешно записались, ".$curUser['name'].". Спасибо.";
                $already = $token;
              }
            }
        }
      ?>


      <?php

        if (!empty($displaySuccess)) {
          echo('<div class="offset-sm-3 alert alert-success d-flex align-items-center" role="alert">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
          </svg>
          <div>
            '.$displaySuccess.'
          </div>
        </div>');

        include('team_form.php');

        die();
        }

      ?>

<?php

if (!empty($displayWarns)) {
  echo '<div class="offset-sm-3 alert alert-success d-flex align-items-center" role="alert">
  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
  </svg>
  <div>
    '.$displayWarns.'
  </div>
</div>';
include('team_form.php');
die();
}

?>

<?php

if (!empty($displayError)) {
  echo '<div class="offset-sm-3 alert alert-warning d-flex align-items-center" role="alert">
  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
  </svg>
  <div>
    '.$displayError.'
  </div>
</div>';
die();
}

?>





    <div class="row align-items-center">
      <div class="col-sm-3 text-end d-none d-md-block"></div>
        <div class="col-sm-6">
          <a
            href="https://pohodnik.tk/login?client_id=1&redirect_uri=<?=$redir?>"
            type="button"
            class="btn btn-outline-primary d-block w-100 my-4"
          >
            <?php echo isset($_GET['invite']) ? 'Зарегистрироваться': 'Записаться'; ?> <img src="https://pohodnik.tk/favicon.ico" alt="poh">
          </a>
        </div>
      </div>


<!--
      <div class="divider"><span>или</span></div>



      <?php
        include_once('reg_form.php');
      ?>
-->

    </div>
  </div>
</html>
