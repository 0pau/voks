<?php
    require_once "php/polls.php";
    require_once "php/user_service.php";

    $error = "";
    $pollInfo = "";
    if (!isset($_GET["id"])) {
        $error = "Hibás kérés";
    } else {
        try {
            $pollInfo = getPollInfo(intval($_GET["id"]));
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
        <?php if ($error == "") { ?>
        <div class="section-head">
            <div class="poll-head">
                <h1><?php echo $pollInfo->cim ?></h1>
                <?php if ($pollInfo->status == 1) { ?>
                    <p class="accentText">Aktuális szavazás</p>
                <?php } else if ($pollInfo->status == 0) { ?>
                    <p class="accentText">Közelgő szavazás</p>
                <?php } else { ?>
                    <p class="accentText error">Lejárt szavazás</p>
                <?php } ?>
            </div>
            <?php if (isLoggedIn() && getUserInfo()->admin_e == 1) { ?>
            <a class="flat-btn icn" href="create_poll.php?id=<?php echo $pollInfo->id ?>">
                <span class="material-symbols-rounded">edit</span>
            </a>
            <a class="flat-btn icn" href="api/delete_poll.php?id=<?php echo $pollInfo->id ?>">
                <span class="material-symbols-rounded">delete</span>
            </a>
            <?php } ?>
            <?php if (isLoggedIn() && userHasVoted($pollInfo->id)) { ?>
                <div class="i-voted-badge">
                    <span class="material-symbols-rounded">editor_choice</span>
                    <span>Szavaztam</span>
                </div>
            <?php } ?>
        </div>

        <p class="poll-info-head">A szavazásról</p>
        <p><?php echo $pollInfo->leiras ?></p>
        <p class="poll-info-head">Fontosabb információk a szavazásról</p>
        <div class="gr c3 poll-info-cards">
            <div class="card">
                <p>A szavazás kiírója</p>
                <p><?php echo $pollInfo->teljesNev ?></p>
            </div>
            <div class="card">
                <p>Szavazás kezdete</p>
                <p><?php echo $pollInfo->kezdet ?></p>
            </div>
            <div class="card">
                <p>Szavazás vége</p>
                <p><?php echo $pollInfo->veg ?></p>
            </div>
        </div>
        <?php if ($pollInfo->status == 1) { ?>
            <?php if (!isLoggedIn()) { ?>
                <div class="action-box">
                    <p>A szavazáshoz jelentkezzen be VOKS-fiókjával!</p>
                    <a href='login.php'><button>Bejelentkezés</button></a>
                </div>
            <?php } else { ?>
            <?php if (!userHasVoted($pollInfo->id)) { ?>
                <div class="action-box">
                    <p>Ön még nem adta le szavazatát. Éljen a lehetőséggel!</p>
                    <a href='vote.php?id=<?php echo $pollInfo->id ?>'><button>Szavazás megkezdése</button></a>
                </div>
                <?php } ?>
            <?php } ?>
        <?php } ?>
        <hr class="o25">
        <?php if ($pollInfo->status == 2 || $pollInfo->elo_e == 1) { ?>
            <p class="poll-info-head">Eredmények</p>
            <?php $results = getPollResults($pollInfo->id); ?>
                <?php if (sizeof($results) != 0) { ?>
                <div class="poll-results">
                    <div class="candidates">
                        <?php
                            $pieChartBg = "";
                            $p = 0;
                            foreach ($results as $candidate) {
                                $pieChartBg = $pieChartBg.$candidate->szin." ".$p."%,";
                                $pieChartBg = $pieChartBg.$candidate->szin." ".$candidate->szazalek+$p."%,";
                                $p = $candidate->szazalek+$p;
                                printf('
                                <div class="candidate-result">
                                    <div>
                                        <span>%s</span>
                                        <span>%d szavazat / %d%%</span>
                                    </div>
                                    <div class="pb"><div style="width: %d%%; background-color: %s"></div></div>
                                </div>
                                ', $candidate->nev, $candidate->szavazatok, $candidate->szazalek, $candidate->szazalek, $candidate->szin);
                            }
                            $pieChartBg = substr($pieChartBg, 0, -1);
                        ?>
                    </div>
                    <div class="pie-chart-container">
                        <div class="pie-chart" style="background-image: conic-gradient(<?php echo $pieChartBg ?>)"></div>
                    </div>
                </div>
                <?php } else { ?>
                    <span>Még senki sem adott le szavazatot.</span>
                <?php } ?>
        <?php } ?>
        <p class="poll-info-head">Jelöltek</p>
        <div class="gr c2">
        <?php
                $count = 0;
                foreach (getCandidates($pollInfo->id) as $j) {
                    $count += 1;
                    printf('
                    <div class="card candidate">
                        <p>%s</p>
                        <p>%s, %s éves</p>
                        <p>%s</p>
                    </div>
                    ', $j->nev, $j->foglalkozas, $j->kor, $j->program);
                }
                if ($count == 0) {
                    echo "<span>Ehhez a szavazáshoz nem társítottak jelölteket.</span>";
                }

                ?>
        </div>
        <?php } else {?>
            <p>Sajnáljuk, hiba történt.</p>
            <h1 class="error"><?php echo $error ?></h1>
        <?php }?>
    </section>
</body>
</html>