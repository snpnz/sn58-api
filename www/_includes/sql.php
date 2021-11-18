<?php
function getSql($filename, $replasesArr=array()) {
    $sql = file_get_contents($filename);

    uksort($replasesArr, function ($a, $b) {
        return (strlen($a) == strlen($b) ? strcmp($a, $b) : strlen($b) - strlen($a));
    });

    foreach($replasesArr as $k => $v) {
        if (is_bool($v) && strpos($sql, "--@$k") != false) {
            $sql = str_replace("--@$k", $v ? "" : "--", $sql);
        } else {
            $sql = str_replace("@$k", $v, $sql);
        }
    }

    return $sql;
}

function nullOrStringInQuotes($val) {
    return empty($val) ? "NULL" : "'$val'";
}
