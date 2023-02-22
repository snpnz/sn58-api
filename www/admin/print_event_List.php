<!doctype html>
<html lang="ru">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->

    <title>sn.fednik.ru event list print  admin</title>
    <style>
       * {
        font-family: "Times New Roman", arial, verdana, tahoma;
        border-collapse: collapse;
       }
    </style>
  </head>
  <body>

    <table border="1" cellpadding="4" cellspacing="0">
        <thead>
            <tr>
                <th>№</th>
                <th>Участник</th>
                <th>Команда</th>
                <th>Время</th>
                <th>Примечание</th>
            </tr>
        </thead>
        <tbody>
    <?php
      if (isset($_GET['id'])) {
        echo "<section class='pp'>";
        include_once('../_includes/db.php');
        $q = $mysqli->query("SELECT
          events.name as eventname,
          event_members.token,
          event_members.team,
          event_members.name as username,
          event_members.surname,
          users.photo,
          event_members.accepted_at
        FROM event_members
            LEFT JOIN events ON event_members.id_event = events.id
            LEFT JOIN users on users.id=event_members.id_user
        WHERE events.id = ".$_GET['id']."
         ORDER BY `event_members`.`created_at`, event_members.surname");
        if (!$q) {
          die($mysqli->error);
        }
        $i = 1;
        while($r = $q -> fetch_assoc()){

          $nam = $r['surname'].'&nbsp;'.$r['username'];
          if (empty($r['username']) && empty($r['surname'])) {
            $nam = 'Участник события';
          }

          $ph = "";
          if (!empty($r['photo'])) {
            $ph .= '<img src="'.$r['photo'].'" width="16" height="16">';
          }

        echo '<tr>';
                echo "<td align=\"right\">{$i}</td>";
                echo "<td>{$nam}</td>";
                echo '<td>'.(!empty($r['team']) ? '<small>'.$r['team'].'</small>' : '').'</td>';
                echo '<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
                echo '<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
        echo '</tr>';



          $i++;
        }
      }
    ?>

</tbody></table>


  </body>
</html>
