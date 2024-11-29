<?php

    include "../php/user_service.php";
    include "../php/db.php";

    adminGuard("..");

    $id = intval($_GET["id"]);

    global $db;
    $stmt = $db->prepare("DELETE FROM szavazasok WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: ..");
