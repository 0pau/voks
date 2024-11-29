<?php
    require_once "php/candidates.php";
    require_once "php/user_service.php";

    $current_tab = "candidates";
    if (isset($_GET["show"])) {
        $current_tab = $_GET["show"];
    }

    adminGuard(".");

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
            <h1>Adminisztráció</h1>
        </div>
        <div class="tab-bar">
            <a href="admin.php" class="<?php echo $current_tab=="candidates"?"active":""?>">Jelöltek</a>
            <a href="?show=users" class="<?php echo $current_tab=="users"?"active":""?>">Felhasználók</a>
        </div>
        <?php if ($current_tab == "candidates") { ?>
            <a href="candidate_info.php"><button class="mb">Új jelölt felvétele</button></a>
            <table>
                <tr>
                    <th>Név</th>
                    <th>Foglalkozás</th>
                    <th>Születési dátum</th>
                    <th>Program</th>
                    <th></th>
                </tr>
                <?php
                    foreach (getAllCandidates() as $candidate) {
                        printf("
                            <tr>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td class='f'>
                                    <div>
                                        <a href='candidate_info.php?id=%s' class='flat-btn'><span class='material-symbols-rounded'>info</span></a>
                                        <a href='api/delete_candidate.php?id=%s' class='flat-btn'><span class='material-symbols-rounded'>delete</span></a>
                                    </div>
                                </td>
                            </tr>
                        ", $candidate->nev, $candidate->foglalkozas, $candidate->szuldatum, $candidate->program, $candidate->id, $candidate->id);
                    }
                ?>
            </table>
        <?php } ?>
        <?php if ($current_tab == "users") { ?>
            <table>
                <tr>
                    <th>Név</th>
                    <th>Leadott szavazatok</th>
                    <th>Email cím</th>
                    <th></th>
                </tr>
                <?php
                foreach (getAllUsersAndVoteCount() as $user) {
                    printf("
                            <tr>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td class='f'>
                                    <div>
                                        <a href='api/toggle_user_privilege.php?name=%s' class='flat-btn'><span class='material-symbols-rounded'>keyboard_double_arrow_%s</span></a>
                                        <a href='api/delete_user.php?name=%s' class='flat-btn'><span class='material-symbols-rounded'>delete</span></a>
                                    </div>
                                </td>
                            </tr>
                        ", $user->nev, $user->leadottak, $user->email, $user->username, $user->admin_e?"down":"up", $user->username);
                }
                ?>
            </table>
        <?php } ?>
    </section>
</body>
</html>