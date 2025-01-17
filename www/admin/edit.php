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

    <title>sn58 Добавление точки</title>
  </head>
  <body>
    <?php
        include_once('../_includes/db.php');
        if (isset($_GET['id'])) {
            $q = $mysqli->query("SELECT * FROM points WHERE id=".intval($_GET['id']));
            $r = $q -> fetch_assoc();
        }

        if (isset($_POST['del'])) {
            $q = $mysqli->query("DELETE FROM `points` WHERE id=".intval($_POST['del'])."");
            
            if(!$q) {
                die('<div class="alert alert-danger m-5" role="alert">'.$mysqli->error.'</div>');
            } else {
                die('<div class="alert alert-success m-5" role="alert">Удалено <a href="/admin/" class="alert-link">Перейти к списку</a></div>');
            }
            
        }

        if (isset($_POST['name']) && isset($_POST['code']) && isset($_POST['description']) && isset($_POST['point'])) {
            $q = $mysqli->query("".( isset($_GET['id']) ? "UPDATE" : "INSERT INTO" )."
                `points`
            SET
                `id_point_group` = ".(intval($_POST['id_point_group']) > 0 ? intval($_POST['id_point_group']) : 'NULL').",
                `name` = '".$mysqli->real_escape_string(trim($_POST['name']))."',
                `point` = '".$mysqli->real_escape_string(trim($_POST['point']))."',
                `description` = '".$mysqli->real_escape_string(trim($_POST['description']))."',
                `code` = '".$mysqli->real_escape_string(trim($_POST['code']))."',
                ".( isset($_GET['id']) ? "`updated_at` = NOW(),": "`created_at` = NOW()," )."
                `id_author` = '".$uid."'
            ".( isset($_GET['id']) ? "WHERE id=".intval($_GET['id']) : "" )."
            ");
            
            if(!$q) {
                die('<div class="alert alert-danger m-5" role="alert">'.$mysqli->error.'</div>');
            }

            if($mysqli->affected_rows === 1) {
                die('<div class="alert alert-success m-5" role="alert">'.(!empty($mysqli->insert_id)?'Добавлено':'Сохранено').'. <a href="/admin/#point'.(isset($mysqli->insert_id)?$mysqli->insert_id:$_GET['id']).'" class="alert-link">Перейти к списку</a></div>');
            }
            
        }
    ?>
    <section class="container">
    <header class="d-flex justify-content-between align-items-center my-4">
          <h1><?=(isset($_GET['id'])?'Редактирование':'Добавление')?> точки</h1>
          <div>
            <a type="button" class="btn btn-outline-primary btn-sm" href="/admin/">К списку</a>
          </div>
      </header>
        <form method="post">
            <div class="row mb-3">
                <div class="col-sm-8">
                    <label for="name" class="form-label">Название</label>
                    <input type="text" class="form-control" id="name" name="name" aria-describedby="nameinfo"
                    required autofocus value="<?=(isset($_GET['id'])?$r['name']:'')?>">
                    <div id="nameinfo" class="form-text"></div>
                </div>
                <div class="col-sm-4">
                <a href="groups.php" id="nameinfo"  class="form-text" style="float: right">управление</a>
                    <label for="name" class="form-label">Группа</label>
                    <select
                        class="form-control"
                        id="id_point_group"
                        name="id_point_group"
                        value="<?=(isset($_GET['id'])?$r['id_point_group']:'')?>"
                    >
                        <option value="0">- без группы -</option>
                        <?php
                                      $q = $mysqli->query("
                                      SELECT
                                        id, name
                                      FROM points_groups ORDER BY created_at DESC");
                                      if(!$q) { die($mysqli->error);}
                                     
                              
                                     while($o = $q -> fetch_assoc()){
                                         echo '<option value="'.$o['id'].'" '.($o['id'] === $r['id_point_group'] ? 'selected': '').'>'.$o['name'].'</option>';
                                     }
                        ?>
                    </select>
                </div>
            </div>
            <div class="input-group mb-3">
                <input type="text" class="form-control" readonly
                value="<?=(isset($_GET['id'])?$r['code']:'')?>"
                aria-label="gen" aria-describedby="codebtn" id="code" name="code">
                <button class="btn btn-outline-secondary" type="button" id="codebtn" <?=(isset($_GET['id'])?"disabled":'')?> >Сгенерировать</button>
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                QR-Код
                </button>

                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">QR-КОД</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img id="img" src="https://chart.apis.google.com/chart?cht=qr&chs=350x350&chl=https://sn.fednik.ru/?code=<?=(isset($_GET['id'])?$r['code']:'')?>">
                        <div class="mt-3">
                            <input type="text" readonly class="form-control" id="copyqr" value="https://sn.fednik.ru/?code=<?=(isset($_GET['id'])?$r['code']:'')?>">
                            <div id="nameinfo" class="form-text">Ссылка для генирации кода в других программах</div>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Описание</label>
                <textarea rows="3" required class="form-control" id="description" name="description"><?=(isset($_GET['id'])?$r['description']:'')?></textarea>
            </div>
            <div class="mb-3">
                <input
                    type="text"
                    required
                    class="form-control"
                    id="point"
                    name="point"
                    placeholder="Вставить координаты или тык в карту"
                    pattern="-?\d+\.\d{4,},-?\d+\.\d{4,}"
                    value="<?=(isset($_GET['id'])?$r['point']:'')?>"
                >
                <div id="map" style="width: 100%; height: 400px"></div>
            </div>
            <button type="submit" class="btn btn-primary btn-block w-100">Сохранить</button>
        </form>
        <?=(isset($_GET['id'])?'<form method="post" onsubmit="ondel" class="mt-2 mb-6"><input type="hidden" name="del" value="'.intval($_GET['id']).'"><button type="submit" class="btn btn-danger">Удалить</button></form>':'')?>
    </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->
    <script>
        var marker;
        var map = L.map('map').setView([53.19526585323435,45.025150537539965], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        map.on('click', addMarker);

        function addMarker(e){
            // Add marker to map at click location; add popup window
            if (marker) {
                map.removeLayer(marker);
            }
            marker = new L.marker(e.latlng).addTo(map);
            point.value = e.latlng.lat + ',' + e.latlng.lng
        }

        function generatePassword() {
            var length = 16,
                charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
                retVal = "";
            for (var i = 0, n = charset.length; i < length; ++i) {
                retVal += charset.charAt(Math.floor(Math.random() * n));
            }
            code.value = retVal;
            img.src= 'https://chart.apis.google.com/chart?cht=qr&chs=350x350&chl=https://sn.fednik.ru/?code=' + retVal;
            copyqr.value= 'https://sn.fednik.ru/?code=' + retVal;
        }

        codebtn.onclick = generatePassword;

        point.oninput = function () {
            if(/-?\d+\.\d{4,},-?\d+\.\d{4,}/.test(point.value)) {
                if (marker) {
                    map.removeLayer(marker);
                }
                const s = point.value.split(',');
                var lt = new L.LatLng(+s[0],+s[1]);
                marker = new L.marker(lt).addTo(map);
                map.panTo(lt);
            }
        }

        <?=(isset($_GET['id'])?"map.panTo(new L.LatLng(".$r['point'].")); marker = new L.marker(new L.LatLng(".$r['point'].")); marker.addTo(map);":'generatePassword();')?>
        function ondel(event) {
            !confirm('Удалить?') && event.preventDefault();
        }
    </script>
  </body>
</html>