<?php

    require_once "db.php";
    require_once "util.php";

    function getPolls($statusFilter = "actual")
    {
        global $db;
        $query = "select users.nev as teljesNev, szavazasok.*, COUNT(szavaz.username) AS szavazatok FROM szavazasok LEFT JOIN szavaz ON szavazasok.id = szavaz.szavazas_id, users WHERE users.username = szavazasok.username";
        if ($statusFilter == "actual") {
            $query = $query . " AND NOW() BETWEEN kezdet AND veg";
        }
        if ($statusFilter == "upcoming") {
            $query = $query . " AND kezdet > NOW()";
        }
        if ($statusFilter == "closed") {
            $query = $query . " AND veg < NOW()";
        }
        $query = $query . " GROUP BY szavazasok.id ORDER BY veg DESC";
        $list = [];
        $r = $db->query($query);

        while ($row = $r->fetch_object()) {

            $row->kezdet = formatDate($row->kezdet, true);
            $row->veg = formatDate($row->veg, true);

            //$row = getPollInfo($row->id, $statusFilter);
            $list[] = $row;
        }

        return $list;
    }


    function getPollInfo($id, $humanReadableDate = true) {
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

        if ($humanReadableDate) {
            $obj->kezdet = formatDate($obj->kezdet, true);
            $obj->veg = formatDate($obj->veg, true);
        }

        return $obj;

    }

    function getVoteCount($pollId) {
        global $db;
        $stmt = $db->prepare("SELECT COUNT(*) AS szavazatok FROM szavaz WHERE szavazas_id = ?");
        $stmt->bind_param("i", $pollId);
        $stmt->execute();
        $r = $stmt->get_result();
        return $r->fetch_object()->szavazatok;
    }

    function userHasVoted($pollId) {
        if (!isLoggedIn()) {
            return false;
        }

        global $db;
        $stmt = $db->prepare("SELECT username FROM szavaz WHERE username = ? AND szavazas_id = ?");
        $stmt->bind_param("ss", $_SESSION["username"], $pollId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows == 1;
    }

    function getCandidates($pollId) {
        global $db;

        $stmt = $db->prepare("SELECT jeloltek.* FROM jeloltek, jeloltje WHERE jeloltek.id = jeloltje.jelolt_id AND szavazas_id = ?");
        $stmt->bind_param("i", $pollId);
        $stmt->execute();
        $result = $stmt->get_result();

        $r = [];

        while ($row = $result->fetch_object()) {
            $row->kor = floor((time() - strtotime($row->szuldatum))/(60*60*24*365));
            $r[] = $row;
        }

        return $r;
    }

    function vote($pollId) {
        global $db;

        $stmt = $db->prepare("INSERT INTO szavaz VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sii", $_SESSION["username"], $pollId, $_POST["candidate"]);
        $stmt->execute();
        $stmt->close();
        header("Location: poll.php?id=".$pollId);
    }

    function getPollResults($pollId) {

        $colors = [
            "#EF5350",
            "#29B6F6",
            "#FFCA28",
            "#303F9F",
            "#26A69A",
            "#827717",
            "#212121",
            "#C51162",
            "#00838F",
            "#D500F9"
        ];

        global $db;
        $stmt = $db->prepare("SELECT nev, COUNT(*) AS szavazatok, (COUNT(*)/(SELECT COUNT(*) FROM szavaz WHERE szavazas_id = ?))*100 AS szazalek FROM szavaz, jeloltek WHERE jeloltek.id = jelolt_id AND szavazas_id = ? GROUP BY jelolt_id ORDER BY szavazatok DESC");
        $stmt->bind_param("ii", $pollId, $pollId);
        $stmt->execute();
        $result = $stmt->get_result();
        $results = [];
        $i = 0;
        while ($row = $result->fetch_object()) {
            $row->szin = $colors[$i];
            $i += 1;
            $results[] = $row;
        }

        return $results;
    }

    function createPoll($cim, $leiras, $kezdet, $veg, $elo_e, $candidates) {
        global $db;
        $stmt = $db->prepare("INSERT INTO szavazasok (username, cim, leiras, kezdet, veg, elo_e) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $_SESSION['username'], $cim, $leiras, $kezdet, $veg, $elo_e);
        $stmt->execute();
        $stmt->close();

        $stmt = $db->prepare("SELECT id FROM szavazasok ORDER BY id DESC LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $id = $result->fetch_object()->id;

        foreach (json_decode($candidates) as $c) {
            $stmt = $db->prepare("INSERT INTO jeloltje (jelolt_id, szavazas_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $c, $id);
            $stmt->execute();
            $stmt->close();
        }

        return $id;
    }

    function editPollInfo($id, $cim, $leiras, $kezdet, $veg, $elo_e) {
        global $db;
        $stmt = $db->prepare("UPDATE szavazasok SET cim = ?, leiras = ?, kezdet = ?, veg = ?, elo_e = ? WHERE id = ?");
        $stmt->bind_param("ssssii", $cim, $leiras, $kezdet, $veg, $elo_e, $id);
        $stmt->execute();
        $stmt->close();
    }