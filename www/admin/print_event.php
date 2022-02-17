<!doctype html>
<html lang="ru">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>sn58.tk admin</title>
    <style>
      .pp {
        padding:15mm;
        display: flex;
        align-items: flex-start;
        flex-wrap: wrap;

      }
      .ololo {
        height: 48mm;
        min-height: 48mm;
        max-height: 48mm;
        width: 88mm;
        min-width: 88mm;
        max-width: 88mm;
        outline: 1px dashed red;
        margin: 1mm;
      
        display: flex;
      }
      .ololo footer {
        padding: 5mm;
        margin-left: -5mm;
      }

      .ololo pre {
        padding-top: 3mm;
        line-height: 1.1em;
      }

      .ololo footer small {
          display: block;
          color: green;
          line-height: 1.1em;
          margin-bottom: 0.2em;
        }
        .ololo footer b {
          display: block;
          line-height: 1.1em;
        }
        .ololo aside {
          text-align: center;
        }
        .ololo aside span {
          display: block;
          margin-top: -5mm;
          font-size: 0.8em;
        }
    </style>
  </head>
  <body>
    <div class="container row">
        <div class="col-sm-6">
          <form>
            <input type="hidden" name="id" value="<?=$_GET['id']?>">
            <label for="info" class="form-label">
              Информация для каждой карточки
            </label>
              <textarea
                rows="4"
                class="form-control"
                id="info"
                name="info"
              ><?=$_GET['info']?></textarea>
              <button type="submit" class="btn btn-primary mt-3">Сохранить</button>
          </form>
        </div>
        <div class="col-sm-6">
        </div>
    </div>
    

        
    
    <?php
      if (isset($_GET['id'])) {
        echo "<section class='pp'>";
        include_once('../_includes/db.php');
        $q = $mysqli->query("SELECT
          events.name as eventname, event_members.token, event_members.name as username, event_members.surname, event_members.accepted_at FROM event_members
        LEFT JOIN events ON event_members.id_event = events.id WHERE events.id = ".$_GET['id']);
        if (!$q) {
          die($mysqli->error);
        }
        $i = 1;
        while($r = $q -> fetch_assoc()){
          echo '
          <div class="ololo">
            <aside>
            <img
              src="https://chart.apis.google.com/chart?cht=qr&chs=350x350&chl=https://sn58.tk/?invite='.$r['token'].'"
              alt="'.$r['token'].'"
              height="80%"
            >
            <span>участник №'.$i.'</span>
            </aside>
            <footer>
              <small>'.$r['eventname'].'</small>
              <b>'.($r['username'].' '.$r['surname']).' '.(empty($r['accepted_at']) ? '~' : '').'</b>
              <pre>'.$_GET['info'].'</pre>
            </footer>
          </div>';

          $i++;
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