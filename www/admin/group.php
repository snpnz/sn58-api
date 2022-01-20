<?php
include_once('../_includes/keeper_admin.php')
?>
<!doctype html>
<html lang="ru">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="" />

    <title>sn58 Добавление группы</title>
  </head>
  <body>
    <?php
        include_once('../_includes/db.php');
        if (isset($_GET['id'])) {
            $q = $mysqli->query("SELECT * FROM points_groups WHERE id=".intval($_GET['id']));
            $r = $q -> fetch_assoc();
        }

        if (isset($_POST['del'])) {
            $q = $mysqli->query("DELETE FROM `points_groups` WHERE id=".intval($_POST['del'])."");
            
            if(!$q) {
                die('<div class="alert alert-danger m-5" role="alert">'.$mysqli->error.'</div>');
            } else {
                die('<div class="alert alert-success m-5" role="alert">Удалено <a href="/admin/groups.php" class="alert-link">Перейти к списку</a></div>');
            }
            
        }

        if (isset($_POST['name']) && isset($_POST['description'])) {
            $q = $mysqli->query("".( isset($_GET['id']) ? "UPDATE" : "INSERT INTO" )."
                `points_groups`
            SET
                 `name` = '".$mysqli->real_escape_string(trim($_POST['name']))."',
                `description` = '".$mysqli->real_escape_string(trim($_POST['description']))."',
                ".( isset($_GET['id']) ? "`updated_at` = NOW(),": "`created_at` = NOW()," )."
                `id_author` = '".$uid."'
            ".( isset($_GET['id']) ? "WHERE id=".intval($_GET['id']) : "" )."
            ");
            
            if(!$q) {
                die('<div class="alert alert-danger m-5" role="alert">'.$mysqli->error.'</div>');
            }

            if($mysqli->affected_rows === 1) {
                die('<div class="alert alert-success m-5" role="alert">'.(!empty($mysqli->insert_id)?'Добавлено':'Сохранено').'. <a href="/admin/groups.php#group'.(isset($mysqli->insert_id)?$mysqli->insert_id:$_GET['id']).'" class="alert-link">Перейти к списку</a></div>');
            }
            
        }
    ?>
    <section class="container">
    <header class="d-flex justify-content-between align-items-center my-4">
          <h1><?=(isset($_GET['id'])?'Редактирование':'Добавление')?> Группы</h1>
          <div>
            <a type="button" class="btn btn-outline-primary btn-sm" href="/admin/groups.php">К списку</a>
          </div>
      </header>
        <form method="post">
            <div class="row mb-3">
                <div class="col-sm-12">
                    <label for="name" class="form-label">Название</label>
                    <input type="text" class="form-control" id="name" name="name" aria-describedby="nameinfo"
                    required autofocus value="<?=(isset($_GET['id'])?$r['name']:'')?>">
                    <div id="nameinfo" class="form-text"></div>
                </div>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Описание</label>
                <textarea rows="3" required class="form-control" id="description" name="description"><?=(isset($_GET['id'])?$r['description']:'')?></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-block w-100">Сохранить</button>
        </form>
        <?=(isset($_GET['id'])?'<form method="post" onsubmit="ondel" class="mt-2 mb-6"><input type="hidden" name="del" value="'.intval($_GET['id']).'"><button type="submit" class="btn btn-danger">Удалить</button></form>':'')?>
    </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    
  </body>
</html>