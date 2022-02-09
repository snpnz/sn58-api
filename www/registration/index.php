<!doctype html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>sn58.tk registration</title>
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
  <?php
        include_once('../_includes/db.php');
        $q = $mysqli->query("
        SELECT events.* FROM events WHERE id=".intval($_GET['event']));
        if(!$q) { die($mysqli->error);}
       $event = $q -> fetch_assoc();
  ?>
  <div class="px-4 py-5 my-5 text-center">
    <h1 class="display-5 fw-bold"><?=$event['name']?></h1>
    <div class="col-lg-6 mx-auto">
      <p class="lead"><?=$event['description']?></p>
      <p class="lead"><?=date('d.m.Y H:i', strtotime($event['start_at']))?> — <?=date('d.m.Y H:i', strtotime($event['finish_at']))?></p>
<?php
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
?>
      <a href="https://www.strava.com/oauth/authorize?client_id=73436&response_type=code&approval_prompt=auto&redirect_uri=<?=$redir?>" type="button" class="btn btn-outline-primary btn-block w-100 my-4">
        Записаться в 1 клик через Strava
        <img src="https://d3nn82uaxijpm6.cloudfront.net/favicon-16x16.png?v=dLlWydWlG8" alt="strava">
  </a>
      

      <div class="divider"><span>или</span></div>



      <form class="mt-4 pt-4" method="POST" action="/registration/?event=<?=$_GET['event']?>">

      <?php
        $displayError = "";
        $displaySuccess = "";
        if (isset($_POST['name']) && isset(($_POST['surname'])) && isset(($_POST['email']))) {
          $q = $mysqli->query("
            SELECT id FROM users WHERE login='".intval($_POST['email'])."'");
          if(!$q) {
            $displayError = $mysqli->error;
          } else {
            $user = $q -> fetch_assoc();
          }


            $q = $mysqli->query("
            SELECT id FROM event_members
            WHERE ".(isset($user) ? 'id_user='.$user['id'] : "login='".$mysqli->real_escape_string(trim($_POST['email']))."'")."
            AND id_event=".intval($_GET['event'])." LIMIT 1 ");
            if(!$q) {
              $displayError = $mysqli->error;
            } else if ($q->num_rows == 1){
              $displayError = "Вы уже записались на это событие";
            }
          

            if (empty($displayError)) {


              $token = md5("snpnz-invite".time().$mysqli->real_escape_string(trim($_POST['email'])));

              $q = $mysqli->query("
                INSERT INTO
                    `event_members`
                SET
                    `id_event` = ".intval($_GET['event']).",
                    `created_at` = NOW(),
                    `id_author` = ".(isset($user) ? $user['id'] : 'NULL').",
                    `id_user` = ".(isset($user) ? $user['id'] : 'NULL').",
                    `name` = '".$mysqli->real_escape_string(trim($_POST['name']))."',
                    `surname` = '".$mysqli->real_escape_string(trim($_POST['surname']))."',
                    `login` = '".$mysqli->real_escape_string(trim($_POST['email']))."',
                    `token` = '".$token."',
                    `accepted_at` = ".(isset($user) ? 'NOW()' : 'NULL')."
              ");
              if(!$q) {
                $displayError = $mysqli->error;
              } else {
                $displaySuccess = "Вы успешно записались. Спасибо.";
              }
            }
        }
      ?>

      <?php
        if (isset($_GET['token']) && isset($_GET['id'])) {
          $q = $mysqli->query("
            SELECT id, login, name, surname FROM users WHERE id='".intval($_GET['id'])."'");
          if(!$q) {
            $displayError = $mysqli->error;
          } else {
            $user = $q -> fetch_assoc();
          }


            $q = $mysqli->query("
            SELECT id FROM event_members
            WHERE id_user='".$user['id']."'"."
            AND id_event=".intval($_GET['event'])." LIMIT 1 ");
            if(!$q) {
              $displayError = $mysqli->error;
            } else if ($q->num_rows == 1){
              $displayError = "Вы уже записались на это событие";
            }


            if (empty($displayError)) {


              $token = md5("snpnz-invite".time().$user['id']);

              $q = $mysqli->query("
                INSERT INTO
                    `event_members`
                SET
                    `id_event` = ".intval($_GET['event']).",
                    `created_at` = NOW(),
                    `id_author` = ".$user['id'].",
                    `id_user` = ".$user['id'].",
                    `name` = '".$user['name']."',
                    `surname` = '".$user['surname']."',
                    `login` = '".$user['email']."',
                    `token` = '".$token."',
                    `accepted_at` = NOW()
              ");
              if(!$q) {
                $displayError = $mysqli->error;
              } else {
                $displaySuccess = "Вы успешно записались. Спасибо.";
              }
            }
        }
      ?>

      <?php

        if (!empty($displaySuccess)) {
          die('<div class="alert alert-success d-flex align-items-center" role="alert">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
          </svg>
          <div>
            '.$displaySuccess.'
          </div>
        </div>');
        }
      
      ?>

<?php

if (!empty($displayError)) {
  echo '<div class="alert alert-danger d-flex align-items-center" role="alert">
  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
  </svg>
  <div>
    '.$displayError.'
  </div>
</div>';
}

?>

      <div class="mb-3 row">
          <label for="surname" class="col-sm-3 col-form-label text-md-end text-start ">Фамилия</label>
          <div class="col-sm-9">
            <input
              type="text"
              autofocus
              required
              autocomplete="surname"
              class="form-control"
              id="surname"
              name="surname"
              pattern="[А-Я][а-я]+"
              value="<?=$_POST['surname']?>"
            >
          </div>
        </div>

      <div class="mb-3 row">
        <label for="name" class="col-sm-3 col-form-label text-md-end text-start">Имя</label>
          <div class="col-sm-9">
            <input
              type="text"
              required
              autocomplete="name"
              class="form-control"
              id="name"
              name="name"
              pattern="[А-Я][а-я]+\s?-?\s?([А-Я][а-я]+)?"
              value="<?=$_POST['name']?>"
            >
          </div>
        </div>

        <div class="mb-3 row">
          <label for="email" class="col-sm-3 col-form-label text-md-end text-start">E-mail</label>
          <div class="col-sm-9">
            <input
              type="email"
              required
              autocomplete="email"
              class="form-control"
              id="email"
              name="email"
              value="<?=$_POST['email']?>"
            >
          </div>
        </div>
        <div class="row">
          <div class="col-sm-3  col-form-label "></div>
          <div class="col-sm-9 text-start">
          <button type="submit" class="btn btn-outline-primary">Записаться</button>
          </div>
          
        </div>
      </form>
      

    </div>
  </div>
</html>