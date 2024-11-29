<?php
    require_once "php/polls.php";
    require_once "php/user_service.php";

    $error = "";
    $pollInfo = "";

    if (!isLoggedIn()) {
        $error = "Bejelentkezés nélkül nem lehet voksolni.";
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!isset($_POST["candidate"]) || !isset($_POST["pollId"])) {
            $error = "A szavazás sikertelen volt.";
        } else {
            vote($_POST["pollId"]);
        }
    } else if ($_SERVER["REQUEST_METHOD"] == "GET") {
        if (!isset($_GET["id"])) {
            $error = "Hibás kérés";
        } else {
            try {
                $pollInfo = getPollInfo(intval($_GET["id"]));
                if (userHasVoted($pollInfo->id)) {
                    throw new Exception("Ön ezen a szavazáson már leadta szavazatát.");
                }
                if ($pollInfo->status == 2) {
                    throw new Exception("Ez a szavazás már lejárt.");
                }
                if ($pollInfo->status == 0) {
                    throw new Exception("E szavazáson még nem nem lehet voksolni.");
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
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
    <form class="section" method="post">
        <?php if ($error == "") { ?>
        <input type="hidden" name="pollId" value="<?php echo $pollInfo->id ?>">
        <div class="poll-head">
            <h1><?php echo $pollInfo->cim ?></h1>
        </div>
        <?php if ($pollInfo->status == 1) { ?>
                <p class="poll-info-head">Jelöltek</p>
                <div class="gr c2">
                    <?php

                    foreach (getCandidates($pollInfo->id) as $j) {
                        printf('
                    <label class="card candidate">
                        <p>%s</p>
                        <p>%s, %s éves</p>
                        <p>%s</p>
                        <input type="radio" name="candidate" value="%s" />
                    </label>
                    ', $j->nev, $j->foglalkozas, $j->kor, $j->program, $j->id);
                    }

                    ?>
                </div>
        <?php } else { ?>
            <span>Ezen a szavazáson nem lehet szavazni.</span>
        <?php } ?>
            <hr class="o25">
            <div class="action-box">
                <p>Ha kiválasztotta a legszimpatikusabb jelöltet, a gomb megnyomásával véglegesítheti szavazatát.</p>
                <a href='vote.php?id=<?php echo $pollInfo->id ?>'><button>Szavazat leadása</button></a>
            </div>
        <?php } else {?>
            <p>Sajnáljuk, hiba történt.</p>
            <h1 class="error"><?php echo $error ?></h1>
        <?php }?>
    </form>
</body>
</html>