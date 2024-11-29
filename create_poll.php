<?php
    require_once "php/polls.php";
    require_once "php/user_service.php";

    $current = "";

    function checkField($fieldName) {
        return !empty($_POST[$fieldName]);
    }

    function getField($fieldName, $defaultValue = "") {
        if (!checkField($fieldName)) {
            return $defaultValue;
        }
        return trim($_POST[$fieldName]);
    }

    function convertHTMLDate($d) {
        $d = explode("T", $d);
        return $d[0]." ".$d[1].":00";
    }

    function getInfo($field) {
        global $current;
        if ($current != "") {
            return $current->$field;
        }
        return "";
    }

    $error = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        try {
            if (!checkField("cim") || !checkField("leiras") || !checkField("kezdet") || !checkField("veg") || (!isset($_POST["edit"]) && !checkField("candidates"))) {
                throw new Exception("Töltsön ki minden mezőt!");
            }

            $cim = getField("cim");
            $leiras = getField("leiras");
            $leiras = str_replace("\n", "<br>", $leiras);
            $kezdet = convertHTMLDate(getField("kezdet"));
            $veg = convertHTMLDate(getField("veg"));
            $candidates = getField("candidates");
            $elo_e = getField("elo_e", 0);
            if ($elo_e == "on") {
                $elo_e = 1;
            }

            if (isset($_POST["edit"])) {
                editPollInfo($_POST["edit"], $cim, $leiras, $kezdet, $veg, $elo_e);
                header("Location: poll.php?id=" . $_POST["edit"]);
            } else {
                $id = createPoll($cim, $leiras, $kezdet, $veg, $elo_e, $candidates);
                header("Location: poll.php?id=" . $id);
            }

        } catch (Exception $e) {
            $error = $e->getMessage();
        }

    } else if (isset($_GET["id"])) {
        $id = $_GET["id"];
        try {
            $current = getPollInfo($id, false);
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
    <?php if (isLoggedIn()) { ?>
    <section>
    <form onsubmit="prepareSubmit(event)" method="post">
        <input type="hidden" id="candidates" name="candidates">
        <?php if ($current == "") { ?>
            <div class="poll-head">
                <h1>Új szavazás kiírása</h1>
                <p>Töltse ki az alábbi űrlapot egy új szavazás kiírásához. Minden mező kötelező, legalább 2 jelöltet meg kell adni.</p>
            </div>
        <?php } else { ?>
            <input type="hidden" name="edit" value="<?php echo $current->id ?>">
            <div class="poll-head">
                <h1>Szavazás szerkesztése</h1>
            </div>
        <?php } ?>
        <?php if ($error != "") { ?>
            <p class="error"><?php echo $error ?></p>
        <?php } ?>

        <div class="create-form">
            <label>
                <p class="poll-info-head">Szavazás címe</p>
                <input type="text" name="cim" value="<?php echo getInfo('cim') ?>">
            </label>
            <label>
                <p class="poll-info-head">Szavazás leírása</p>
                <textarea name="leiras"><?php echo getInfo('leiras') ?></textarea>
            </label>
            <div class="group">
                <label>
                    <p class="poll-info-head">Szavazás kezdete</p>
                    <input type="datetime-local" name="kezdet" value="<?php echo getInfo('kezdet') ?>">
                </label>
                <label>
                    <p class="poll-info-head">Szavazás vége</p>
                    <input type="datetime-local" name="veg" value="<?php echo getInfo('veg') ?>">
                </label>
            </div>
            <label class="switchbox">
                <div>
                    <span>Élő szavazás</span>
                    <span>A szavazás lezárása előtt a felhasználók megtekinthetik a szavazás pillanatnyi állását</span>
                </div>
                <input type="checkbox" name="elo_e" <?php if (getInfo('elo_e') == 1) echo('checked') ?>>
            </label>
            <?php if ($current != "") { ?>
                <p class="poll-info-head">Jelöltek</p>
                <p>A jelöltek listáját a szavazás kiírása után nem lehet módosítani, mert az megtévesztené a szavazókat. Új jelöltlista készítéséhez hozzon létre egy másik szavazást.</p>
            <?php } else {?>
            <div class="two-sided-box">
                <div>
                    <p class="poll-info-head">Elérhető jelöltek</p>
                    <div class="l" id="availableList"></div>
                </div>
                <div>
                    <div class="btn" onclick="addCandidates()"><span class="material-symbols-rounded">arrow_forward</span></div>
                    <div class="btn" onclick="removeCandidates()"><span class="material-symbols-rounded">arrow_back</span></div>
                </div>
                <div>
                    <p class="poll-info-head">Társított jelöltek</p>
                    <div class="l" id="addedList"></div>
                </div>
            </div>
            <?php }?>
        </div>
    </form>
    <div class="action-box">
        <p></p>
        <button onclick="prepareSubmit()">
            <?php if ($current != "") { ?>
                Módosítások mentése
            <?php } else {?>
                Szavazás kiírása
            <?php } ?>
        </button>
    </div>
    </section>
    <?php } else { ?>
    <section>
        <p>Csak bejelentkezett felhasználók írhatnak ki szavazásokat.</p>
    </section>
    <?php } ?>
</body>
<script>

    <?php if ($current != "") {echo "const editMode = true;"; } else {echo "const editMode = false;";} ?>
    if (!editMode) {
        getAvailable();
    }
    async function getAvailable() {
        let xhr = new XMLHttpRequest();
        xhr.open("GET", "api/candidates.php");
        xhr.onload = () =>{
            let target = document.getElementById("availableList");
            let p = JSON.parse(xhr.response);
            for (let c of p) {
                let i = document.createElement("label");
                i.setAttribute("cId", c.id);
                i.innerHTML = c.nev+" ("+c.szuldatum+")"+"<input type='checkbox'>";
                target.appendChild(i);
            }
        }
        xhr.send();
    }

    function moveCandidates(f, t) {
        let from = document.getElementById(f);
        let to = document.getElementById(t);

        let c = [];

        for (let i of from.children) {
            if (i.getElementsByTagName("input")[0].checked) {
                i.getElementsByTagName("input")[0].checked = false;
                c.push(i);
            }
        }

        for (let i of c) {
            to.appendChild(i);
        }

    }

    function addCandidates() {
        moveCandidates("availableList", "addedList");
    }

    function removeCandidates() {
        moveCandidates("addedList", "availableList");
    }

    function prepareSubmit() {
        if (!editMode) {
            if (document.getElementById("addedList").childElementCount < 2) {
                alert("Legalább 2 jelöltet kell társítani a szavazáshoz!");
                return;
            }

            let h = document.getElementById("candidates");

            let c = [];
            for (let i of document.getElementById("addedList").children) {
                c.push(i.getAttribute("cId"));
            }
            h.value = JSON.stringify(c);

            document.getElementsByTagName("form")[0].requestSubmit();
        } else {
            document.getElementsByTagName("form")[0].requestSubmit();
        }
    }
</script>
</html>