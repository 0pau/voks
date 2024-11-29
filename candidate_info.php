<?php
    require_once "php/candidates.php";
    require_once "php/user_service.php";

    function checkField($fieldName) {
        return !empty($_POST[$fieldName]);
    }
    function getField($fieldName, $defaultValue = "") {
        if (!checkField($fieldName)) {
            return $defaultValue;
        }
        return trim($_POST[$fieldName]);
    }


adminGuard();

    $error = "";
    $current = new stdClass();
    $current->nev = "";
    $current->program = "";
    $current->foglalkozas = "";
    $current->szuldatum = "2000-01-01";
    $id = "";

    try {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if (!checkField("nev") || !checkField("foglalkozas") || !checkField("szuldatum") || !checkField("program")) {
                echo "<script>alert('Töltsön ki minden mezőt!')</script>";
            }

            $current->nev = getField("nev");
            $current->foglalkozas = getField("foglalkozas");
            $current->szuldatum = getField("szuldatum");
            $current->program = getField("program");

            if (isset($_POST["edit"])) {
                updateCandidate(getField("id"), $current);
            } else {
                saveCandidate($current);
            }
            header("Location: admin.php");
        } else if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
            $id = $_GET["id"];
            $current = getCandidate($id);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VOKS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href="." id="logo">
            <img src="img/logo.png" id="logo">
            <span>VOKS</span>
        </a>
        <?php if (!isLoggedIn()) { ?>
            <a href="login.php"><button>Bejelentkezés</button></a>
        <?php } else { ?>
            <div class="user-menu">
                <span class="material-symbols-rounded">person</span>
                <span class="user-name"><?php echo getUserInfo()->nev ?></span>
                <div class="submenu">
                    <?php if (getUserInfo()->admin_e == 1) { ?>
                        <a href="admin.php">Adminisztráció</a>
                    <?php } ?>
                    <a href="logout.php">Kijelentkezés</a>
                </div>
            </div>
        <?php } ?>
    </nav>
    <?php if ($error == "") { ?>
    <section>
    <form method="post">
        <?php
        if ($id!="") {
            echo "<input type='hidden' name='edit' value='1'>";
            echo "<input type='hidden' name='id' value='".$id."'>";
        } ?>
        <div class="section-head">
            <h1>Jelölt adatai</h1>
        </div>
        <div class="create-form">
            <label>
                <p class="poll-info-head">Név</p>
                <input type="text" name="nev" value="<?php echo $current->nev ?>">
            </label>
            <label>
                <p class="poll-info-head">Foglalkozás</p>
                <input type="text" name="foglalkozas" value="<?php echo $current->foglalkozas ?>">
            </label>
            <label>
                <p class="poll-info-head">Születési dátum</p>
                <input type="date" name="szuldatum" value="<?php echo $current->szuldatum ?>">
            </label>
            <label>
                <p class="poll-info-head">Program</p>
                <textarea name="program"><?php echo $current->program ?></textarea>
            </label>
        </div>
        <div class="action-box">
            <p></p>
            <button>Mentés</button>
        </div>
    </form>
    </section>
    <?php } else { ?>
    <section>
        <p><?php echo $error ?></p>
    </section>
    <?php } ?>
</body>
</html>