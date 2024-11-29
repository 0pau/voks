<?php

    include "../php/user_service.php";
    include "../php/db.php";

    adminGuard("..");

    $id = $_GET["name"];

    if ($id == "root") {
        echo "<script>alert('A fő rendszergazdai fiók nem törölhető.');document.location='../admin.php?show=users'</script>";
        exit();
    }

    global $db;
    $stmt = $db->prepare("DELETE FROM users WHERE username = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();

    header("Location: ../admin.php?show=users");
