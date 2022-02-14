<?php
    if (!empty($already)) {
        $q = $mysqli->query("SELECT id_event, team FROM event_members WHERE token = '{$already}' LIMIT 1");
        $r = $q -> fetch_assoc();
        $q = $mysqli->query("SELECT DISTINCT team FROM event_members WHERE id_event = ".$r['id_event']);

echo '<form method="post">
    <div class="mb-3 row">
        <label for="team" class="col-sm-3 col-form-label text-md-end text-start ">Команда</label>
        <div class="col-sm-9">
        <input
            type="text"
            autofocus
            autocomplete="team"
            class="form-control"
            list="teams" id="team" name="team"
            value="'.(isset($_POST['team'])? $_POST['team']:$r['team']).'"
        >
        <input type="hidden" name="teamupdatetoken" value="'.$already.'">
        <div class="form-text text-start">Вы можете указать название команды за которую выступаете или оставить поле пустым</div>
        </div>
    </div>
    <div class="mb-3 row">
        <label for="team" class="col-sm-3 col-form-label text-md-end text-start ">&nbsp;</label>
        <div class="col-sm-9 text-start">
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
    </div>
</form>
';
        echo '<datalist id="teams">';
        while ($r = $q->fetch_assoc()) {
            if (!empty($r['team'])){
                echo '    <option value="'.$r['team'].'">';
            }
        }
        echo '</datalist>';
    } else {
        echo 'no already';
    }

    if (isset($_POST['team']) && isset($_POST['teamupdatetoken'])) {
        $tok = $_POST['teamupdatetoken'];
        $team = trim($mysqli->real_escape_string($_POST['team']));

        $q = $mysqli -> query("UPDATE event_members SET team='{$team}' WHERE token='{$tok}'");
        if ($q) {
            echo '<div class="offset-sm-3 alert alert-success" role="alert">
            '.(empty() ? 'Вы указали что участвуете без команды': 'Вы успешно выбрали команду "'.$team.'"').'
          </div>';
        }
    }
?>