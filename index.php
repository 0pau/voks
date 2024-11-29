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
    <section>
        <div class="section-head">
            <h1>Szavazások</h1>
            <?php if (isLoggedIn()) { ?>
            <a href="create_poll.php"><button>Új szavazás kiírása</button></a>
            <?php } ?>
        </div>
        <div class="tab-bar">
            <a href="." class="<?php echo $current_filter=="actual"?"active":""?>">Aktuális szavazások</a>
            <a href="?show=upcoming" class="<?php echo $current_filter=="upcoming"?"active":""?>">Közelgő szavazások</a>
            <a href="?show=closed" class="<?php echo $current_filter=="closed"?"active":""?>">Lezárt szavazások</a>
            <a href="?show=all" class="<?php echo $current_filter=="all"?"active":""?>">Összes</a>
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
                                <p>%d szavazat</p>
                                <hr class="v">
                                <span class="material-symbols-rounded">access_time</span>
                                <p>%s - %s</p>
                            </div>
                           </a>', $poll->id, $poll->teljesNev, $poll->cim, $poll->szavazatok, $poll->kezdet, $poll->veg);
                }
                if (sizeof($polls) == 0) {
                    echo "<span>Nincsenek szavazások ebben a kategóriában.</span>";
                }
            ?>
        </div>
    </section>
</body>
</html>