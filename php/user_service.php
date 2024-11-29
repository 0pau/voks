<?php

    session_start();

    function adminGuard($t = "") {
        if (!isLoggedIn() || getUserInfo()->admin_e == 0) {
            echo "<script>alert('Ez az oldal csak rendszergazdai jogosultsággal rendelkező felhasználók számára érhető el.');document.location='$t'</script>";
        }
    }

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

        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        $stmt = $db->prepare("SELECT username, jelszo FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows != 1) {
            throw new Exception("Hibás felhasználónév vagy jelszó.");
        }

        $row = $result->fetch_object();
        //For debug purposes only!
        if ($row->jelszo == "" || password_verify($password, $row->jelszo)) {
            $_SESSION['username'] = $username;
            touchUser();
            header("Location: .");
        } else {
            throw new Exception("Hibás felhasználónév vagy jelszó.");
        }
    }

    function touchUser() {
        global $db;
        $username = $_SESSION['username'];
        $stmt = $db->prepare("UPDATE users SET utolso_login = NOW() WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->close();
    }

    function register() {

        if (!isset($_POST['username']) || !isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['password-confirm'])) {
            throw new Exception("Töltsön ki minden mezőt.");
        }

        if ($_POST["password"] != $_POST["password-confirm"]) {
            throw new Exception("A jelszavak nem egyeznek.");
        }

        $data = [
            'username' => trim($_POST['username']),
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email']),
            'password' => trim($_POST['password'])
        ];

        if (userExists($data['username'])) {
            throw new Exception("A felhasználónév foglalt.");
        }
        if (emailExists($data['email'])) {
            throw new Exception("Ezzel az email címmel már regisztráltak.");
        }
        global $db;
        $stmt  = $db->prepare("INSERT INTO users (username, nev, email, jelszo) VALUES (?, ?, ?, ?)");
        $passwd = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt->bind_param("ssss", $data['username'], $data['name'], $data['email'], $passwd);
        $stmt->execute();
        $stmt->close();
        header("Location: login.php");
    }

    function userExists($username) {
        global $db;
        $stmt = $db->prepare("SELECT username FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows != 0;
    }

    function emailExists($email) {
        global $db;
        $stmt = $db->prepare("SELECT email FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows != 0;
    }

    function getUserInfo() {
        global $db;
        $stmt = $db->prepare("SELECT username, nev, admin_e, email FROM users WHERE username = ?");
        $stmt->bind_param("s", $_SESSION['username']);
        $stmt->execute();
        return $stmt->get_result()->fetch_object();
    }

    function getAllUsersAndVoteCount() {
        global $db;
        $stmt = $db->prepare("select users.username, users.nev, users.email, users.admin_e, COUNT(jelolt_id) AS leadottak from users LEFT JOIN szavaz ON users.username = szavaz.username GROUP BY users.username ORDER BY users.nev");
        $stmt->execute();
        $result = $stmt->get_result();
        $r = [];
        while ($row = $result->fetch_object()) {
            $r[] = $row;
        }
        return $r;
    }