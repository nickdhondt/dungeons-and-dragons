<!doctype html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Dungeons and Dragons</title>
    <script src="js/game.js"></script>
    <link rel="stylesheet" href="css/screen.css"/>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
</head>
<body>
<div id="login_holder">
    <div>
        <h1>Login</h1>
        <ul>
            <li>
                <input type="text" placeholder="Gebruikersnaam" id="txt_username" autofocus/>
            </li>
            <li>
                <input type="password" placeholder="Wachtwoord" id="txt_password"/>
            </li>
            <li>
                <input type="button" value="Inloggen" id="btn_login"/>
            </li>
        </ul>
    </div>
</div>
<div id="game_area">
    <header>
        <div id="user_info"></div>
        <div id="logout" class="button">
            Uitloggen
        </div>
    </header>
    <div id="show_admin">
        <div id="admin_user"></div>
        <div id="admin_panel">
            <div id="admin_tabs">
                <ul>
                    <li id="admin_one" class="active">Gebruikers</li>
                    <li id="admin_two">Nieuwe gebruiker</li>
                </ul>
            </div>
            <div class="clearfix"></div>
            <div id="admin_tab_one" class="admin_tab"></div>
            <div id="admin_tab_two" class="admin_tab">
                <ul>
                    <li>
                        <input type="text" placeholder="Gebruikersnaam" id="txt_new_username"/>
                    </li>
                    <li>
                        <input type="password" placeholder="Wachtwoord" id="txt_new_password"/>
                    </li>
                    <li>
                        <input type="button" value="Registreer" id="btn_register"/>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div id="container">
        <nav id="tabs">
            <ul>
                <li class="active" id="one">Basic</li>
                <li id="two">Levelling</li>
                <li id="three">Skill Assigning</li>
            </ul>
        </nav>
        <div class="clearfix"></div>
        <div id="tab_one" class="tab_area">
            <h1>Een</h1>
        </div>
        <div id="tab_two" class="tab_area">
            <h1>Twee</h1>
        </div>
        <div id="tab_three" class="tab_area">
            <h1>Drie</h1>
        </div>
    </div>
</div>
<div id="notifications"></div>
</body>
</html>