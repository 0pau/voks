<?php
    require_once "php/polls.php";
    require_once "php/user_service.php";

    $current_filter = "actual";
    if (isset($_GET["show"])) {
        $current_filter = $_GET["show"];
    }

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VOKS</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>
<body>
    <nav>
        <div id="logo">
            <img src="img/logo.png" id="logo">
            <span>VOKS</span>
        </div>
        <?php if (!isLoggedIn()) { ?>
            <a href="login.php"><button>Bejelentkezés</button></a>
        <?php } else { ?>
            <a href="logout.php"><button>Kijelentkezés</button></a>
        <?php } ?>
    </nav>
    <section>
        <div class="section-head">
            <h1>Szavazások</h1>
            <?php if (isLoggedIn()) { ?>
            <button>Új szavazás kiírása</button>
            <?php } ?>
        </div>
        <div class="tab-bar">
            <a href="." class="<?php echo $current_filter=="actual"?"active":""?>">Aktuális szavazások</a>
            <a href="?show=upcoming" class="<?php echo $current_filter=="upcoming"?"active":""?>">Közelgő szavazások</a>
            <a href="?show=closed" class="<?php echo $current_filter=="closed"?"active":""?>">Lezárt szavazások</a>
        </div>
        <div class="list">
            <?php
                $polls = getPolls($current_filter);
                foreach ($polls as $poll) {
                    printf('<a href="poll.php?id=%s" class="card poll">
                            <div>
                                <span class="material-symbols-rounded">person</span>
                                <p>%s</p>
                            </div>
                            <p>%s</p>
                            <div>
                                <span class="material-symbols-rounded">select_check_box</span>
                                <p>0 szavazat</p>
                                <hr class="v">
                                <span class="material-symbols-rounded">access_time</span>
                                <p>%s - %s</p>
                            </div>
                           </a>', $poll->id, $poll->teljesNev, $poll->cim, $poll->kezdet, $poll->veg);
                }
                if (sizeof($polls) == 0) {
                    echo "<span>Nincsenek szavazások ebben a kategóriában.</span>";
                }
            ?>
        </div>
    </section>
</body>
</html>