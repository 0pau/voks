<?php

    session_start();

    function isLoggedIn()
    {
        return isset($_SESSION['username']);
    }

    function logIn()
    {
        if (!isset($_POST['username']) || !isset($_POST['password'])) {
            throw new Exception("Töltsön ki minden mezőt.");
        }
        global $db;

        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $db->prepare("SELECT username, jelszo FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows != 1) {
            throw new Exception("Hibás felhasználónév vagy jelszó.");
        }

        $row = $result->fetch_object();
        if (password_verify($password, $row->jelszo)) {
            $_SESSION['username'] = $username;
            header("Location: .");
        } else {
            throw new Exception("Hibás felhasználónév vagy jelszó.");
        }
    }