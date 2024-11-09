<?php
    require_once "php/polls.php"
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
        <button>Bejelentkezés</button>
    </nav>
    <section>
        <div class="section-head">
            <h1>Szavazások</h1>
            <button>Új szavazás kiírása</button>
        </div>
        <h4>Aktuális szavazások</h4>
        <div class="list">
            <?php
                $polls = getPolls();
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
                           </a>', $poll->id, $poll->userFullName, $poll->title, $poll->startDate, $poll->endDate);
                }
            ?>
        </div>
    </section>
</body>
</html>