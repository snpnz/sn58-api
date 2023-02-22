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
        font-family:  'Arial narrow', arial;
      }
      .ololo {
        height: 44mm;
        min-height: 44mm;
        max-height: 44mm;
        width: 80mm;
        min-width: 80mm;
        max-width: 80mm;
        outline: 1px dashed #a0a0a0;
        margin: 1px;
        margin-bottom: 2px;
        padding: 2mm;
        display: inline-flex;
        flex-direction: column;
        float: left;
      }

      .ololo .main {
        display: flex;
        flex-grow: 2;
      }
      .ololo article {
        min-width: 0;
      }

      .ololo article pre {
        padding-top: 2mm;
        line-height: 1.1em;
        font-size: 0.8em;
        overflow: visible;
      }

.ololo aside {
position: relative;}
      .ololo img.qr {
        width: 35mm;
        height: 35mm;

        margin-top: -2mm;
      }

      .userpic {
        position: absolute;
        top: 14mm;
        left: 15mm;
        z-index: 3;
        border: 2px solid #fff;
        width: 5mm;
        height: 5mm;
        border-radius: 50%;
      }

      .ololo article small:first-child {
          display: block;
          color: green;
          line-height: 0.9em;
          margin-top: 2mm;
        }

        .ololo article small em {
          text-overflow: ellipsis;
          overflow: hidden;
          white-space: nowrap;
          color: red;
          display: block;
          font-size: 0.8em;
        }

        .ololo article b {
          display: block;
          line-height: 1.1em;
        }
        .ololo aside {
          text-align: center;
          margin-left: -2mm;
        }
        .ololo aside span {
          display: block;
          margin-top: -4mm;
          font-size: 0.8em;
          font-weight: bold;
        }

        .ololo footer {
          border-top: 1px dotted #adadad;
          font-size: 0.8em;
          margin-bottom: -1mm;
          text-align: right;
          margin-top: auto;
        }
    </style>
  </head>
  <body>
    <div class="container">
          <form>
            <div class="row">
            <div class="col-sm-6">
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
            </div>
            <div class="col-sm-6">
            <input type="hidden" name="id" value="<?=$_GET['id']?>">
            <label for="footer" class="form-label">
              Нижняя строка
            </label>
              <input
                class="form-control"
                id="footer"
                name="footer"
                value="<?=$_GET['footer']?>"
              >
            </div>


            </div>
              <button type="submit" class="btn btn-primary mt-3">Сохранить</button>

          </form>

    </div>




    <?php
      if (isset($_GET['id'])) {
        echo "<section class='pp'>";
        include_once('../_includes/db.php');
        $q = $mysqli->query("SELECT
          events.name as eventname, event_members.token, event_members.team, event_members.name as username, event_members.surname,users.photo, event_members.accepted_at FROM event_members
        LEFT JOIN events ON event_members.id_event = events.id LEFT JOIN users on users.id=event_members.id_user  WHERE events.id = ".$_GET['id']." ORDER BY `event_members`.`created_at`, event_members.surname ");
        if (!$q) {
          die($mysqli->error);
        }
        $i = 1;
        while($r = $q -> fetch_assoc()){

          $nam = $r['username'].' '.$r['surname'];
          if (empty($r['username']) && empty($r['surname'])) {
            $nam = 'Участник события';
          }

          $ph = "";
          if (!empty($r['photo'])) {
            $ph .= '<img src="'.$r['photo'].'" class="userpic">';
          }

          echo '
          <div class="ololo">
            <div class="main">
              <aside>
                <img
                class="qr"
                  src="https://chart.apis.google.com/chart?cht=qr&chs=350x350&chl=https://sn.fednik.ru/registration/?invite='.$r['token'].'"
                  alt="'.$r['token'].'"
                >
                '.$ph.'
                <span><small>участник</small> №&thinsp;'.$i.'</span>
              </aside>
              <article>
                <small>'.$r['eventname'].'</small>
                '.(!empty($r['team']) ? '<small><em>'.$r['team'].'</em></small>' : '').'
                <b>'.($nam).'</b>
                <pre>'.$_GET['info'].'</pre>
              </article>
            </div>
            '.(isset($_GET['footer'])? '<footer>'.$_GET['footer'].'</footer>': '').'
          </div>';

          $i++;
        }
        die('</section>');
      }
    ?>

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
