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

    <title>sn.fednik.ru admin</title>
    <style>
      .pp {
        padding:15mm;
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;

      }
      .ololo {
        width: 45mm;
        border: 1px dashed red;
        margin: 1mm;
        text-align:center;
      }
      .ololo footer {
        padding: 5mm;
        padding-top: 0;
        margin-top: -2mm;
      }

      .ololo footer small {
          display: block;
          color: green;
          line-height: 1.1em;
          margin-bottom: 0.1em;
        }
        .ololo footer b {
          line-height: 1.1em;
        }
    </style>
  </head>
  <body>
    <?php
      if (isset($_GET['id'])) {
        echo "<section class='pp'>";
        include_once('../_includes/db.php');
        $q = $mysqli->query("SELECT points.*, points_groups.name as groupname FROM points 
        LEFT JOIN points_groups ON points_groups.id = points.id_point_group WHERE points.id IN(".implode(',', $_GET['id']).")");
        if (!$q) {
          die($mysqli->error);
        }
        while($r = $q -> fetch_assoc()){
          echo '
          <div class="ololo">
            <img
              src="https://chart.apis.google.com/chart?cht=qr&chs=350x350&chl=https://sn.fednik.ru/?code='.$r['code'].'"
              alt="'.$r['code'].'"
              width="100%"
            >
            <footer>
              '.(!empty($r['groupname']) ? '<small>'.($r['groupname']).'</small>' : '').'
              <b>'.$r['name'].'</b>
              
            </footer>
          </div>';
        }
        die('</section>');
      }
    ?>
    <form>
    <section class="container">
      <header class="d-flex justify-content-between align-items-center my-4">
          <h1>Список точек</h1>
          <div>
            <button type="submit" class="btn btn-primary btn-sm">Печать</a>
          </div>
      </header>
    <div class="list-group">
      <?php
        include_once('../_includes/db.php');
        $q = $mysqli->query("SELECT points.*, points_groups.name as groupname FROM points 
        LEFT JOIN points_groups ON points_groups.id = points.id_point_group");
        while($r = $q -> fetch_assoc()){
          echo '
          <li class="list-group-item list-group-item-action "><div class="form-check">
            <input class="form-check-input" name="id[]" type="checkbox" value="'.$r['id'].'" id="point'.$r['id'].'">
            <label class="form-check-label" for="point'.$r['id'].'">
            '.$r['name'].' <small class="text-primary">'.($r['groupname']).'</small>
            </label>
          </div></li>';
        }
      ?>
      </div>
</section>
</form>
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