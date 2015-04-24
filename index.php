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
    </header>
</div>
<div id="notifications"></div>
</body>
</html>