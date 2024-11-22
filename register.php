<?php

    require_once "php/db.php";
    require_once "php/user_service.php";

    $exception = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        try {
            register();
        } catch (Exception $e) {
            $exception = $e->getMessage();
        }
    }

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Regisztráció</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>
<body>
    <div class="login-screen">
        <form class="login-window" method="post">
            <img src="img/logo.png" id="logo">
            <h2>Regisztráció</h2>
            <p>Van már fiókja? <a href="login.php">Jelentkezzen be!</a></p>
            <label>
                Felhasználónév
                <input type="text" name="username">
            </label>
            <label>
                Teljes név
                <input type="text" name="name">
            </label>
            <label>
                Email cím
                <input type="email" name="email">
            </label>
            <label>
                Jelszó
                <input type="password" name="password">
            </label>
            <label>
                Jelszó mégegyszer
                <input type="password" name="password-confirm">
            </label>
            <?php
            if ($exception != "") {
                echo "<span class='error'>$exception</span>";
            }
            ?>
            <button>Regisztráció</button>
        </form>
    </div>
</body>
</html>