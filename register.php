<?php
$inscription = false;
$password_true = true;
$user_exist = false;
$regexp_valid = false;
$secret_answer_valid = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['username'])
        AND !empty($_POST['password'])
        AND !empty($_POST['repeat_password'])) {
        $username = htmlentities(trim($_POST['username']));
        $password = htmlentities(trim($_POST['password']));
        $repeat_password = htmlentities(trim($_POST['repeat_password']));
        $secret_question = "";
        $secret_answer = "";
        if(!empty($_POST['firstname'])){
            $firstname = htmlentities(trim($_POST['firstname']));
        }else{
            $firstname = htmlentities(trim(""));
        }
        if(!empty($_POST['lastname'])){
            $lastname = htmlentities(trim($_POST['lastname']));
        }else{
            $lastname = htmlentities(trim(""));
        }

        if($_POST['secret_question'] != "----- Question secrete -----"){
            $secret_question = $_POST['secret_question'];
        }else{
            $secret_question = "Nom de votre meilleur prof";
        }
        if(isset($_POST['secret_answer']) AND !empty($_POST['secret_answer'])){
            $secret_answer = $_POST['secret_answer'];
            $secret_answer_valid = true;
        }

        if($password !== $repeat_password) {
            $password_true = false;
        }else {
            if(preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,20}$/', $password) AND preg_match('`^([a-zA-Z0-9-_]{6,12})$`', $username)) {
                $regexp_valid = true;
                $password = md5($password);
                try {
                    require_once("db_connect.php");
                    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $req = "SELECT `username` FROM `users` WHERE username = :username";
                    $stmt = $dbh->prepare($req);
                    $stmt->execute([
                        'username' => $username
                    ]);
                    if($stmt->rowCount() > 0) {
                        $user_exist = true;
                    }else{
                        if(!empty($secret_answer)){
                            $secret_answer_valid = true;
                            $q = "INSERT INTO `users` (`username`, `password`, `firstname`, `lastname`,`secret_question`,`secret_answer`) 
                                    VALUES (:username, :password, :firstname, :lastname, :secret_question, :secret_answer);";
                            $statement = $dbh->prepare($q);
                            $statement->execute([
                                'username' => $username,
                                'password' => $password,
                                'firstname' => $firstname,
                                'lastname' => $lastname,
                                'secret_question' => $secret_question,
                                'secret_answer' => $secret_answer
                            ]);
                            //un dossier pour chaque user
                            mkdir('files/'.$username);
                            $user_exist = false;
                        }else{
                            $secret_answer_valid = false;
                        }
                    }
                }
                catch(PDOException  $e) {
                    die("Error: ".$e);
                }
                $inscription = true;
                $password_true = true;
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
</head>
<body>
<div id="content" role="main">
    <div id="my_header">
        <?php include("header_out.php"); ?>
        <div id="content_box">
            <div id="box_auth">
                <?php
                    if($password_true){
                        if($regexp_valid){
                            if($user_exist){
                                echo '<p class=\'error_message\'><img src=\'img/dislike.png\' alt=\'settings\'/>&nbsp;&nbsp;Le pseudo existe déjà !</p>';
                            }else{
                                if($inscription){
                                    echo "<p class='succes_message'><img src='img/like.png' alt='settings'/>&nbsp;&nbsp;Inscription effectuée ! Cliquez <a href='auth.php'>ici</a> pour se connecter.</p>";
                                }else{
                                    echo '<p class="carreful_message">Les champs (*) sont obligatoires</p>';
                                }
                            }
                        }else if($secret_answer_valid){
                            echo '<p class=\'regex_message\'><img src=\'img/warning.png\' alt=\'settings\'/>&nbsp;&nbsp;<b>Attention :</b>Votre <b>pseudo</b> doit contenir entre 6-12 caractères et le <b>mot de passe</b> 
                                entre 8-20 caractères dont au moins une lettre majuscule et un chiffire ( ne doit pas contenir ces caractères : @#$%)</p>';
                        }else{
                            echo '<p class=\'regex_message\'><img src=\'img/warning.png\' alt=\'settings\'/>&nbsp;&nbsp;<b>Attention :</b>Votre <b>pseudo</b> doit contenir entre 6-12 caractères et le <b>mot de passe</b> 
                                entre 8-20 caractères dont au moins une lettre majuscule et un chiffire ( ne doit pas contenir ces caractères : @#$%)</p>';
                        }
                    }else{
                        echo '<p class=\'error_message\'><img src=\'img/dislike.png\' alt=\'settings\'/>&nbsp;&nbsp;Les deux mot de passe doivent être identiques</p>';
                    }
                ?>
                <form action="register.php" method="POST" class="login">
                    <h1>Inscription</h1>
                    <input type="text" name="firstname" class="login-input" placeholder="Nom" autofocus>
                    <input type="text" name="lastname" class="login-input" placeholder="Prénom">
                    <input type="text" name="username" class="login-input" placeholder="Pseudo*">
                    <input type="password" name="password" class="login-input" placeholder="Mot de passe*">
                    <input type="password" name="repeat_password" class="login-input" placeholder="Confirmation mot de passe*">
                    <select name="secret_question" class="login-input">
                        <option value="----- Question secrète -----">----- Question secrète -----</option>
                        <option value="Nom de votre meilleur prof ?">Nom de votre meilleur prof</option>
                        <option value="Footballeur prefere ?">Footballeur préféré</option>
                        <option value="Plat preferee ?">Plat préféré</option>
                    </select>
                    <input type="text" name="secret_answer" class="login-input" placeholder="réponse secrète">
                    <input type="submit" value="Inscription" class="login-submit">
                </form>
            </div>
            <?php include("aside.php"); ?>
        </div>
    </div>
</div>
</body>

</html>