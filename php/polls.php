<?php

    require_once "db.php";
    require_once "util.php";

    function getPolls($statusFilter = "actual")
    {
        global $db;
        $query = "SELECT id, kezdet, veg FROM szavazasok";
        if ($statusFilter == "actual") {
            $query = $query . " WHERE NOW() BETWEEN kezdet AND veg";
        }
        if ($statusFilter == "upcoming") {
            $query = $query . " WHERE kezdet > NOW()";
        }
        if ($statusFilter == "closed") {
            $query = $query . " WHERE veg < NOW()";
        }
        $list = [];
        $r = $db->query($query);

        while ($row = $r->fetch_object()) {
            $row = getPollInfo($row->id, $statusFilter);
            $list[] = $row;
        }

        return $list;
    }


    function getPollInfo($id) {
        global $db;

        if (empty($id) || !is_numeric($id)) {
            throw new Exception("Hibás azonosító");
        }

        $query = "SELECT *, users.nev AS teljesNev FROM szavazasok INNER JOIN users ON szavazasok.username = users.username WHERE id = ?";

        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $r = $stmt->get_result();

        if ($r->num_rows != 1) {
            throw new Exception("A kért szavazás nem található.");
        }

        $obj = $r->fetch_object();

        $r->close();

        $start = strtotime($obj->kezdet);
        $end = strtotime($obj->veg);
        $obj->status = 1;
        if (time() > $end) {
            $obj->status = 2;
        } else if (time() < $start) {
            $obj->status = 0;
        }

        $obj->kezdet = formatDate($obj->kezdet, true);
        $obj->veg = formatDate($obj->veg, true);

        return $obj;

    }

    function userHasVoted($pollId) {
        if (!isLoggedIn()) {
            return "false";
        }
        return "true";
    }



