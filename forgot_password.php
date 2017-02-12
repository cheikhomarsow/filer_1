<?php
    $password_change = false;
    $secret_question = "";
    $secret_answer = "";
    $username = "";
    require_once("db_connect.php");
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!empty($_POST["forgot_password_username"])) {
            $username = $_POST["forgot_password_username"];
            $req = "SELECT * FROM `users` WHERE username = :username";
            $statement = $dbh->prepare($req);
            $statement->bindValue(':username', $username, PDO::PARAM_STR);
            $statement->execute();
            if ($statement->rowCount() > 0) {
                $user_exist = true;
                $question = $statement->fetch();
                $secret_question = $question['secret_question'];
                $secret_answer = $question['secret_answer'];
            }else{
                header('Location:auth.php');
            }
        }
        if (!empty($_POST["forgot_password_question"]) AND !empty($_POST["forgot_password_answer"])
            AND !empty($_POST["password"]) AND !empty($_POST["repeat_password"])
        ) {
            $secret_question = $_POST["forgot_password_question"];
            $secret_answer = $_POST["forgot_password_answer"];
            $req = "SELECT * FROM `users` WHERE secret_question = :secret_question";
            $statement = $dbh->prepare($req);
            $statement->bindValue(':secret_question', $secret_question, PDO::PARAM_STR);
            $statement->execute();
            if ($statement->rowCount() > 0) {
                $row = $statement->fetch();
                if ($secret_answer == $row['secret_answer'] AND $_POST["password"] == $_POST["repeat_password"]) {
                    $password_change = true;
                    $password = md5($_POST["password"]);
                    $req = "UPDATE `users` SET `password` = :password  WHERE `secret_answer` = :secret_answer";
                    $statement = $dbh->prepare($req);
                    $statement->execute(array(
                        'password' => $password,
                        'secret_answer' => $secret_answer
                    ));
                }
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
                <?php
                    if($password_change){
                        echo "<p class='succes_message'>Votre mot de passe a été modifié !<br>
                                cliquez <a href='auth.php'>ici</a> pour se connecter avec votre nouveau mot de passe</p>";
                    }else{
                        echo '<p class=\'regex_message\'><b>Attention :</b> Vérifier que les 2 mot de passe sont identiques et que la réponse est bonne !</p>';
                    }
                ?>
                <div id="forgot_passwor" class="login">
                    <h5>Nouveau mot de passe</h5>
                    <form action="forgot_password.php" method="POST">
                        <?php
                            echo " <input type=\"text\" name=\"forgot_password_question\" class=\"login-input\" value='".$secret_question."'>
                                    <input type=\"text\" name=\"forgot_password_answer\" class=\"login-input\" placeholder='Votre réponse ici...'>
                                    <input type=\"password\" name=\"password\" class=\"login-input\" placeholder='Nouveau mot de passe...'>
                                    <input type=\"password\" name=\"repeat_password\" class=\"login-input\" placeholder='Vérification mot de passe...'>
                                    <input type=\"submit\" value=\"Valider\" class=\"login-submit\">";
                        ?>

                    </form>
                </div>
            </div>
            <?php include("aside.php"); ?>
        </div>
    </div>
</div>
</body>

</html>