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

    <title>sn58.tk checkpoints</title>
  </head>
  <body>
    <section class="container">
      <header class="d-flex justify-content-between align-items-center my-4">
          <h1>Список отметок на точках</h1>
          <div>
            *
          </div>
      </header>
    <div class="list-group">
      <?php
        include_once('../_includes/db.php');
          $q = $mysqli->query("
          SELECT
            users.*,
            points.*,
            points_reports.*,
            CONCAT_WS(' ', users.name, users.surname) as user,
            points_groups.name as groupname
          FROM points_reports
            LEFT JOIN points ON points_reports.id_point = points.id
            LEFT JOIN users ON users.id = points_reports.id_user
            LEFT JOIN points_groups ON points_groups.id = points.id_point_group
          ORDER BY points_reports.created_at DESC");
          if(!$q) { die($mysqli->error);}
         
  
         while($r = $q -> fetch_assoc()){
          echo '<a href="/admin/?id='.$r['id_point'].'" id="point'.$r['id'].'"
          class="list-group-item list-group-item-action d-flex align-items-center">
            <img src="'.$r['photo'].'" height="50" alt="😊" class="m-r-2">
            <div style="flex-grow: 2" class="p-2">
              <div class="d-flex w-100 justify-content-between">
                <span class="mb-0"><b>'.$r['user'].'</b> отметился на точке <b>'.$r['name'].'</b></span>
                <small>'.($r['groupname']).'</small>
                <small>'.($r['created_at']).'</small>
              </div>
              <p class="mb-1">
                <code>'.$r['coordinates'].'</code>
                <em><small>'.$r['comment'].'</small></em>
              </p>
            </div>
        </a>';
          }
      ?>
      </div>
</section>
    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->
  </body>
</html>