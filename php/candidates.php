<?php

    require_once "db.php";
    require_once "util.php";

    function getAllCandidates() {
        global $db;
        $query = "SELECT * FROM jeloltek ORDER BY nev";
        $statement = $db->prepare($query);
        $statement->execute();
        $result = $statement->get_result();
        $r = [];
        while ($row = $result->fetch_object()) {
            $r[] = $row;
        }

        return $r;
    }

    function getCandidate($id) {
        global $db;
        $query = "SELECT * FROM jeloltek WHERE id = ?";
        $statement = $db->prepare($query);
        $statement->bind_param("i", $id);
        $statement->execute();
        $result = $statement->get_result();
        if ($result->num_rows == 0) {
            throw new Exception("A keresett jelölt nem található.");
        }
        return $result->fetch_object();
    }

    function updateCandidate($id, $candidateObj) {
        global $db;
        $query = "UPDATE jeloltek SET nev = ?, szuldatum = ?, program = ? WHERE id = ?";
        $statement = $db->prepare($query);
        $statement->bind_param("sssi", $candidateObj->nev, $candidateObj->szuldatum, $candidateObj->program, $id);
        $statement->execute();
    }

    function saveCandidate($candidateObj) {
        global $db;
        $query = "INSERT INTO jeloltek (nev, szuldatum, program, foglalkozas) VALUES (?, ?, ?, ?)";
        $statement = $db->prepare($query);
        $statement->bind_param("ssss", $candidateObj->nev, $candidateObj->szuldatum, $candidateObj->program, $candidateObj->foglalkozas);
        $statement->execute();
    }