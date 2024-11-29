<?php

    include "../php/user_service.php";
    include "../php/db.php";

    adminGuard("..");

    $id = $_GET["name"];

    if ($id == "root") {
        echo "<script>alert('A fő rendszergazdai fiók nem módosítható.');document.location='../admin.php?show=users'</script>";
        exit();
    }

    global $db;
    $stmt = $db->prepare("SELECT admin_e FROM users WHERE username=?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_object()->admin_e;

    $new_status = 0;
    if ($result == 0) {
        $new_status = 1;
    }

    $stmt = $db->prepare("UPDATE users SET admin_e = ? WHERE username=?");
    $stmt->bind_param("ss", $new_status, $id);
    $stmt->execute();

    header("Location: ../admin.php?show=users");
