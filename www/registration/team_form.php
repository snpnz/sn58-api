<?php
    if (!empty($already)) {
        $q = $mysqli->query("SELECT id_event, team FROM event_members WHERE token = '{$already}' LIMIT 1");
        $r = $q -> fetch_assoc();
        $q = $mysqli->query("SELECT DISTINCT team FROM event_members WHERE id_event = ".$r['id_event']);


        echo '<datalist id="teams">';
        while ($r = $q->fetch_assoc()) {
            if (!empty($r['team'])){
                echo '    <option value="'.$r['team'].'">';
            }
        }
        echo '</datalist>';
    }

    if (isset($_POST['team']) && isset($_POST['teamupdatetoken'])) {
        $tok = $_POST['teamupdatetoken'];
        $team = trim($mysqli->real_escape_string($_POST['team']));

        $q = $mysqli -> query("UPDATE event_members SET team='{$team}' WHERE token='{$tok}'");
        if ($q) {
            echo '<div class="offset-sm-2 p-1 list-item text-success d-flex align-items-center" role="alert">
                    '.(empty($team) ? '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                      <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                    </svg>' : '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                                                   <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7Zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216ZM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/>
                                                 </svg>').'

                  &nbsp;&nbsp;'.(empty($team) ? 'Вы указали что участвуете без команды': 'Вы успешно выбрали команду «'.$team.'»').'
                  <button class="btn btn-sm btn-circle" onClick="teamForm.classList.toggle(\'d-none\')"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                                                                        <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                                                                                      </svg></button>
                  </div>';
        }
    }

    echo '<form method="post" id="teamForm" class="mt-2 '.(empty($team) ? '' : 'd-none').'">
        <div class="mb-3 row">
            <label for="team" class="col-sm-2 col-form-label text-md-end text-start ">Команда</label>
            <div class="col-sm-9">
            <input
                type="text"
                autofocus
                autocomplete="organization"
                class="form-control"
                list="teams" id="team" name="team"
                value="'.(isset($_POST['team'])? $_POST['team']:$r['team']).'"
            >
            <input type="hidden" name="teamupdatetoken" value="'.$already.'">
            <div class="form-text text-start">Вы можете указать название команды за которую выступаете или оставить поле пустым</div>
            </div>
        </div>
        <div class="mb-3 row">
            <label for="team" class="col-sm-2 col-form-label text-md-end text-start ">&nbsp;</label>
            <div class="col-sm-9 text-start">
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </form>
    ';
?>
