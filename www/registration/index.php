<?php
  include_once('../_includes/db.php');
  include_once('../_includes/sql.php');
  global $mysqli;

  $invite = "";
  $event_member_data = null;
  $id_event = null;

  // invite это код из QR-кода карточки участника ( = полю token из БД event_members)
  if (isset($_GET['invite']) && !empty($_GET['invite'])) {
    $invite = $mysqli->real_escape_string($_GET['invite']);
  }


  if (!empty($invite)) {
    $file = __DIR__ . '/sql/event_member_by_token.sql';
    $sql = getSql($file, array('token' => nullOrStringInQuotes($invite)));
    $q = $mysqli->query($sql);

    if (!$q) { die("Wrong event_member query. ".$mysqli->error); }
    if ($q -> num_rows == 0) { die("Incorrect token or invite code"); }
    $event_member_data = $q->fetch_assoc();
    $id_event = $event_member_data['id_event'];
  } else {
    $id_event = intval($_GET['event']);
  }

  if (empty($id_event)) {
    die('Wrong event (incorrect id)/ Add ?event=1');
  }

  $file = __DIR__ . '/sql/event_by_id.sql';
  $sql = getSql($file, array('id_event' => $id_event));
  $q = $mysqli->query($sql);

  if(!$q) { die("Wrong event data for id #{$id_event}. ".$mysqli->error); }

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
    <title><?=$event['name']?></title>
  </head>
  <body>

  <div class="px-4 py-5 my-5 text-center">
    <h1 class="display-5 fw-bold"><?=$event['name']?></h1>
    <div class="col-lg-6 mx-auto">
      <p class="lead"><?=$event['description']?></p>
      <p class="lead" title="завершение <?=date('d.m.Y H:i', strtotime($event['finish_at']))?>">
          начало: <?=date('d.m.Y в H:i', strtotime($event['start_at']))?>
      </p>
<?php
  $displayError = "";
  $displaySuccess = "";
  $displayWarns = "";
  $displayChecks = "";
  $already = "";
  $token = null;

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
              $title = implode(' → ', array($curUser['name'],date('d.m.Y в H:i', strtotime($rr['created_at'])).""));
              $displayWarns = "<span title=".($title).">Вы подтвердили свое участие ".date('d.m.Y', strtotime($rr['created_at']))."</span>";
              $displayChecks = $curUser['name'].", Вы успешно записались на это событие ".date('d.m.y в H:i', strtotime($rr['created_at']));
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
          echo('<div class="offset-sm-2 alert alert-success d-flex align-items-center" role="alert">
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
  echo '<div class="offset-sm-2 p-1 list-item text-success d-flex align-items-center" role="alert">
<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-patch-check-fill" viewBox="0 0 16 16">
  <path d="M10.067.87a2.89 2.89 0 0 0-4.134 0l-.622.638-.89-.011a2.89 2.89 0 0 0-2.924 2.924l.01.89-.636.622a2.89 2.89 0 0 0 0 4.134l.637.622-.011.89a2.89 2.89 0 0 0 2.924 2.924l.89-.01.622.636a2.89 2.89 0 0 0 4.134 0l.622-.637.89.011a2.89 2.89 0 0 0 2.924-2.924l-.01-.89.636-.622a2.89 2.89 0 0 0 0-4.134l-.637-.622.011-.89a2.89 2.89 0 0 0-2.924-2.924l-.89.01-.622-.636zm.287 5.984-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7 8.793l2.646-2.647a.5.5 0 0 1 .708.708z"/>
</svg>&nbsp;&nbsp;'.$displayWarns.'
</div>';
include('team_form.php');
die();
}

?>

<?php

if (!empty($displayError)) {
  echo '<div class="offset-sm-2 alert alert-warning d-flex align-items-center" role="alert">
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
