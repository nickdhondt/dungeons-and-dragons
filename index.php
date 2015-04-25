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
                <input type="text" placeholder="Gebruikersnaam" id="txt_username"/>
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
        Admin
        <div id="admin_panel">
            <div id="admin_tabs">
                <ul>
                    <li id="admin_one" class="active">Gebruikers</li>
                </ul>
                <ul>
                    <li id="admin_two">Nieuwe gebruiker</li>
                </ul>
            </div>
            <div class="clearfix"></div>
            <div id="admin_tab_one" class="admin_tab">
                <ul>
                    <li>
                        <div>Gebruiker</div>
                        <div>✖</div>
                    </li>
                    <li>
                        <div>Andere gebruiker</div>
                        <div>✖</div>
                    </li>
                    <li>
                        <div>Nog een</div>
                        <div>✖</div>
                    </li>
                </ul>
            </div>
            <div id="admin_tab_two" class="admin_tab">
                <ul>
                    <li>
                        <input type="text" placeholder="Gebruikersnaam"/>
                    </li>
                    <li>
                        <input type="password" placeholder="Wachtwoord"/>
                    </li>
                    <li>
                        <input type="button" value="Registreer"/>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div id="container">
        <nav id="tabs">
            <ul>
                <li class="active" id="one">Tab 1</li>
                <li id="two">Tab 2</li>
                <li id="three">Tab 3</li>
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