<?php

    require_once "db.php";
    require_once "util.php";

    function getPolls()
    {
        global $db;
        $r = mysqli_query($db, "SELECT id FROM poll");
        $list = [];

        while ($row = mysqli_fetch_object($r)) {
            $row = getPollInfo($row->id);
            $list[] = $row;
        }

        return $list;
    }


    function getPollInfo($id) {
        global $db;

        if (empty($id) || !is_numeric($id)) {
            throw new Exception("Hibás azonosító");
        }

        $r = mysqli_query($db, "SELECT *, user.name AS userFullName FROM poll INNER JOIN user ON poll.username = user.username WHERE id = $id");

        if (mysqli_num_rows($r) != 1) {
            throw new Exception("A kért szavazás nem található.");
        }

        $obj = mysqli_fetch_object($r);

        $start = strtotime($obj->startDate);
        $end = strtotime($obj->endDate);
        $obj->status = 1;
        if (time() > $end) {
            $obj->status = 2;
        } else if (time() < $start) {
            $obj->status = 0;
        }

        $obj->startDate = formatDate($obj->startDate, true);
        $obj->endDate = formatDate($obj->endDate, true);

        return $obj;

    }

