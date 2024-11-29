<?php

    include "../php/user_service.php";
    include "../php/db.php";

    adminGuard("..");

    $id = $_GET["id"];

    global $db;
    $stmt = $db->prepare("DELETE FROM jeloltek WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: ../admin.php");
