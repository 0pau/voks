<?php
    require_once "php/polls.php";
    $error = "";
    $pollInfo = "";
    if (!isset($_GET["id"])) {
        $error = "Hibás kérés";
    } else {
        try {
            $pollInfo = getPollInfo($_GET["id"]);
        } catch (Exception $e) {
            $error = $e->getMessage();
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
        <div id="logo">
            <img src="img/logo.png" id="logo">
            <span>VOKS</span>
        </div>
        <button>Bejelentkezés</button>
    </nav>
    <section>
        <?php if ($error == "") { ?>
        <div class="poll-head">
            <h1><?php echo $pollInfo->title ?></h1>
            <?php if ($pollInfo->status == 1) { ?>
            <p class="accentText">Aktuális szavazás</p>
            <?php } else if ($pollInfo->status == 0) { ?>
            <p class="accentText">Közelgő szavazás</p>
            <?php } else { ?>
            <p class="accentText error">Lejárt szavazás</p>
            <?php } ?>
        </div>
        <p class="poll-info-head">A szavazásról</p>
        <p><?php echo $pollInfo->description ?></p>
        <p class="poll-info-head">Fontosabb információk a szavazásról</p>
        <div class="gr c3 poll-info-cards">
            <div class="card">
                <p>A szavazás kiírója</p>
                <p><?php echo $pollInfo->userFullName ?></p>
            </div>
            <div class="card">
                <p>Szavazás kezdete</p>
                <p><?php echo $pollInfo->startDate ?></p>
            </div>
            <div class="card">
                <p>Szavazás vége</p>
                <p><?php echo $pollInfo->endDate ?></p>
            </div>
        </div>
        <?php if ($pollInfo->status == 1) { ?>
        <div class="action-box">
            <p>Ön még nem adta le szavazatát. Éljen a lehetőséggel!</p>
            <button>Szavazás megkezdése</button>
        </div>
        <?php } ?>
        <hr class="o25">
        <?php if ($pollInfo->status == 2) { ?>
            <p class="poll-info-head">Eredmények</p>
            <span>Jelenleg nincsenek eredmények</span>
        <?php } else { ?>
        <p class="poll-info-head">Jelöltek</p>
        <div class="gr c2">
            <div class="card candidate">
                <p>Jelölt neve</p>
                <p>Foglalkozás, kor</p>
                <p>Program</p>
            </div>
            <div class="card candidate">
                <p>Jelölt neve</p>
                <p>Foglalkozás, kor</p>
                <p>Program</p>
            </div>
            <div class="card candidate">
                <p>Jelölt neve</p>
                <p>Foglalkozás, kor</p>
                <p>Program</p>
            </div>
        </div>
        <?php } ?>
        <?php } else {?>
            <p>Sajnáljuk, hiba történt.</p>
            <h1 class="error"><?php echo $error ?></h1>
        <?php }?>
    </section>
</body>
</html>