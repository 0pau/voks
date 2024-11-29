<?php

    include "../php/db.php";
    global $db;
    $stmt = $db->prepare("SELECT * FROM jeloltek ORDER BY nev");
    $stmt->execute();
    $result = $stmt->get_result();
    $r = [];
    while ($row = $result->fetch_object()) {
        $r[] = $row;
    }

    echo json_encode($r, JSON_UNESCAPED_UNICODE);