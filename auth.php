<?php
    $connexion = false;
    $user_exist = false;
    $row = [];
    require_once("db_connect.php");
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!empty($_POST['username'])
            AND !empty($_POST['password'])){
            $username = htmlentities(trim($_POST["username"]));
            $password = htmlentities(trim($_POST["password"]));
            try {
                $password = md5($password);
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $req = "SELECT * FROM `users` WHERE username = :username AND password = :password";
                $stmt = $dbh->prepare($req);
                $stmt->execute([
                    'username' => $username,
                    'password' => $password
                ]);
                if ($stmt->rowCount() == 1) {
                    session_start();
                    $_SESSION['username'] = $username;
                    header('location:my_files.php');
                    $connexion = true;
                }
            }
            catch(PDOException  $e) {
                die("Error: ".$e);
            }
        }


    }

?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="author" content="cosinus">
        <title>COS Filer</title>
        <link rel="stylesheet" href="style/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/script.js"></script>
        <script type="text/javascript" src="js/script2.js"></script>
    </head>
    <body>
    <div id="content" role="main">
        <div id="my_header">
            <?php include("header_out.php"); ?>
            <div id="content_box">
                <div id="box_auth">
                    <?php if(!$connexion && !empty($username) && !empty($password)) echo "<p class='error_message'>pseudo ou mot de passe incorrect</p>"; ?>
                    <form action="auth.php" class="login" method="POST">
                        <h1>Connexion</h1>
                        <input type="text" name="username" class="login-input" placeholder="Pseudo" autofocus>
                        <input type="password" name="password" class="login-input" placeholder="Mot de passe">
                        <input type="submit" value="Se connecter" class="login-submit">
                        <p class="login-help" id="forgot_password">Mot de passe oublié ?</p>
                        <p class="login-help">Ou</p>
                        <p class="login-help"><a href="register.php"><b>Inscription</b></a></p>
                    </form>
                    <div id="forgot_password_box" class="login">
                        <h5>Nouveau mot de passe</h5>
                        <form action="forgot_password.php" method="POST">
                            <input type="text" name="forgot_password_username" class="login-input" placeholder="Pseudo">
                            <input type="submit" value="Valider" class="login-submit">
                        </form>
                    </div>
                </div>
                <?php include("aside.php"); ?>
            </div>
        </div>
    </div>
    </body>

</html>