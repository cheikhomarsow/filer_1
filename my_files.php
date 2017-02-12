<?php
    session_start();
    if(empty($_SESSION['username'])) {
        header('Location:auth.php');
        exit();
    }else {
        $username = $_SESSION['username'];
        require_once "db_connect.php" ;

        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $req = "SELECT `id` FROM `users` WHERE username = :username";
        $stmt = $dbh->prepare($req);
        $stmt->execute([
            'username' => $username
        ]);
        $row = $stmt->fetch();
        $id_user = intval($row['id']);
        $choice = false;
        $add_file = false;
        $file_already_exist = false;
        $file_send = false;
        $doublon = false;
        $user_file = false;

        //add file
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(isset($_FILES['file']['name']) && !empty($_FILES)) {
                $choice = true;
                $file_name = $_FILES['file']['name'];
                $file_extension = strrchr($file_name, '.');
                $file_tmp_name = $_FILES['file']['tmp_name'];
                $file_url = 'files/'.$username . "/" . $file_name;

                $extension_autorisees = array('.jpg', '.jpeg', '.txt', '.png','.pdf');

                //Je vérifie si un fichier de ce nom existe déjà
                $v = "SELECT `file_name` FROM `files` WHERE id_user = :id_user AND file_name = :file_name";
                $stmt_v = $dbh->prepare($v);
                $stmt_v->execute([
                    'id_user' => $id_user,
                    'file_name' => $file_name
                ]);
                if ($stmt_v->rowCount() > 0) {
                    $file_already_exist = true;
                } else {
                    if (in_array($file_extension, $extension_autorisees)) {
                        if (move_uploaded_file($file_tmp_name, $file_url)) {
                            try {
                                $file_send = true;
                                $q = "INSERT INTO `files` (`file_name`, `file_url`, `id_user`,`date`) VALUES (:file_name, :file_url, :id_user, NOW());";
                                $statement = $dbh->prepare($q);
                                $statement->execute([
                                    'file_name' => $file_name,
                                    'file_url' => $file_url,
                                    'id_user' => $id_user
                                ]);
                                $add_file = true;
                            } catch (PDOException  $e) {
                                die("Error: " . $e);
                            }

                        }
                    }
                }
            }
            //delete file
            if (isset($_POST['delete_file'])) {
                $file_url = $_POST['file_name_to_delete'];
                $delete = "DELETE FROM `files` WHERE file_url = :file_url";
                $delete_file = $dbh->prepare($delete);
                $delete_file->execute([
                    'file_url' => $file_url
                ]);
                unlink("$file_url");
                header("Refresh:0");
            }
            //rename file
            if (!empty($_POST['file_name']) AND !empty($_POST['file_url']) AND !empty($_POST['new_name'])) {

                try {
                    $file_name = $_POST['file_name'];
                    $file_url = $_POST['file_url'];
                    $file_extension = strrchr($file_name, '.');

                    $req_id = "SELECT `id` FROM `files` WHERE `file_name` = :file_name";
                    $select_id = $dbh->prepare($req_id);
                    $select_id->execute([
                        "file_name" => $file_name
                    ]);

                    $row = $select_id->fetch();
                    $id = (int)$row['id'];
                    $new_name = $_POST['new_name'] . $file_extension;


                    $file_url = 'files/' . $username . "/" . $file_name;
                    $new_url = 'files/' . $username . "/" . $new_name;

                    //verifions si le nouveau name existe dans notre bdd
                    $req = "SELECT `file_name` FROM `files` WHERE `file_name` = :new_name AND `id_user` = :id_user";
                    $stmt = $dbh->prepare($req);
                    $stmt->bindValue(':new_name', $new_name, PDO::PARAM_STR);
                    $stmt->bindValue(':id_user', $id_user, PDO::PARAM_INT);
                    $stmt->execute();
                    if (isset($_POST['new_name']) AND $stmt->rowCount() > 0) {
                        $doublon = true;
                    } else {
                        if (rename($file_url, $new_url)) {
                            $req = "UPDATE files SET `file_name` = :new_name , `file_url` = :new_url  WHERE `id_user` = :id_user AND `file_name` = :file_name";

                            $statement = $dbh->prepare($req);
                            $statement -> bindValue(':new_name', $new_name, PDO::PARAM_STR);
                            $statement -> bindValue(':new_url', $new_url, PDO::PARAM_STR);
                            $statement -> bindValue(':file_name', $file_name, PDO::PARAM_STR);
                            $statement -> bindValue(':id_user', $id_user, PDO::PARAM_INT);
                            $statement -> execute();

                            header("Refresh:0");
                            exit(0);
                        }
                    }
                } catch (PDOException  $e) {
                    die("Error: " . $e);
                }
            }
            //replace file
            if (!empty($_POST['current_file_name']) AND !empty($_FILES)) {
                $new_file_name = $_FILES['new_file']['name'];
                $new_file_url = 'files/' . $username . "/" . $new_file_name;
                $current_file_name = $_POST['current_file_name'];
                $current_file_url = 'files/' . $username . "/" . $current_file_name;
                //verifions si le name du nouveau file existe dans notre bdd
                $req = "SELECT `file_name` FROM `files` WHERE `file_name` = :new_file_name";
                $stmt = $dbh->prepare($req);
                $stmt->bindValue(':new_file_name', $new_file_name, PDO::PARAM_STR);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $file_already_exist = true;
                }

                $req_id = "SELECT `id` FROM `users` WHERE `username` = :username";
                $select_id = $dbh->prepare($req_id);
                $select_id->execute([
                    "username" => $username
                ]);

                $row = $select_id->fetch();
                $id = (int)$row['id'];

                //verifions si le user a un fichier de ce nom
                $req2 = "SELECT `file_name` FROM `files` WHERE `file_name` = :current_file_name AND `id_user` = :id";
                $stmt2 = $dbh->prepare($req2);
                $stmt2->bindValue(':current_file_name', $current_file_name, PDO::PARAM_STR);
                $stmt2->bindValue(':id', $id, PDO::PARAM_INT);
                $stmt2->execute();
                if ($stmt2->rowCount() > 0) {
                    $user_file = true;
                }

                if (!$file_already_exist) {
                    if ($user_file) {
                        $file_tmp_name = $_FILES['new_file']['tmp_name'];
                        $req_name = "UPDATE files SET `file_name` = :new_file_name  WHERE `id` = :id";
                        $req_url = "UPDATE `files` SET `file_url` = :new_file_url  WHERE `id` = :id";

                        $statement_name = $dbh->prepare($req_name);
                        $statement_name->bindValue(':new_file_name', $new_file_name, PDO::PARAM_STR);
                        $statement_name->bindValue(':id', $id, PDO::PARAM_INT);
                        $statement_name->execute();

                        $statement_url = $dbh->prepare($req_url);
                        $statement_url->bindValue(':new_file_url', $new_file_url, PDO::PARAM_STR);
                        $statement_url->bindValue(':id', $id, PDO::PARAM_INT);
                        $statement_url->execute();

                        if (move_uploaded_file($file_tmp_name,$new_file_url)) {
                            rename($new_file_url, $current_file_url);
                            header("Refresh:0");
                            exit(0);
                        }
                    }
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
<div id="content">
    <?php include("header_in.php"); ?>
    <div id="content_box">
        <div id="box_articles">
            <?php
                if($choice && !empty($file_name)){
                    if(!$file_already_exist){
                        if($file_send && $add_file){
                            echo "<p class='succes_message'><img src='img/like.png' alt='settings'/>&nbsp;&nbsp;Fichier ajouté avec succés</p>";
                        }else{
                            echo "<p class='error_message'><img src='img/dislike.png' alt='settings'/>&nbsp;&nbsp;Format fichier non accepté</p>";
                        }
                    }else{
                        echo "<p class='error_message'><img src='img/dislike.png' alt='settings'/>&nbsp;&nbsp;Ce fichier existe déjà</p>";
                    }
                }
                if(!$user_file){
                    echo "<p class='welcome'><img src='img/warning.png' alt='warning'/>&nbsp;&nbsp;<b>Attention : </b>Mettez un nom de fichier qui vous appartient au cas ou vous voulez le remplacé .</p>";
                }
            ?>
            <div id="div_top">
                <div id="add_file">
                    <h5><img src='img/file.png' alt='settings'/>&nbsp;&nbsp;Ajouter un fichier</h5>
                    <p class='welcome'><b>Formats acceptés : </b>pdf, jpg, jpeg, png, txt</p>
                    <form action="my_files.php" method="POST" enctype="multipart/form-data">
                        <input type="file" name="file" >
                        <input type="submit" name="sumbit" value="Ajouter">
                    </form>
                </div>

                <?php
                    if($doublon) {
                        echo "<p class='error_message'><img src='img/dislike.png' alt='settings'/>&nbsp;&nbsp;Un fichier de ce nom existe déjà !</p>";
                    }
                    echo "<div id='param'>";
                        echo "<h5><img src='img/settings.png' alt='settings'/>&nbsp;&nbsp;Autres paramètres</h5>";
                        echo "<button id='my_button_rename'>Renommer un fichier</button>";
                        echo "<button id='my_button_replace'>Remplacer un fichier</button>";
                    echo "</div>";
                ?>
            </div>
            <div id="replace_box">
                <h5><img src='img/replace.png' alt='settings'/>&nbsp;&nbsp;Selectionner le nouveau fichier</h5>
                <form action="my_files.php" method="POST" enctype="multipart/form-data">
                    <input type="file" name="new_file"><br>
                    <label for="new_name_file" class='welcome'>Nom du fichier a remplacé (avec l'extensioon, ex: <em>.jpg</em>): </label><br>
                    <input type="text" id="new_name_file" name="current_file_name" placeholder="nom du fichier"><br>
                    <input type="submit" value="Valider">
                </form>
            </div>
            <?php
                $q = "SELECT * FROM `files` WHERE id_user=:id_user ORDER BY DATE DESC";
                $my_files = $dbh->prepare($q);
                $my_files->execute([
                    'id_user' => $id_user
                ]);
                if($rows = $my_files-> rowCount() == 0){
                    echo "<div class='not_files'>
                            <h5>Dossier vide</h5>
                            <img src='img/sad.png' alt='vide'/>
                        </div>
                    ";
                }else{
                    $extension_img = array('.jpg','.jpeg','.png','.txt','.pdf');
                    echo "<h5><img src='img/archive.png' alt='inbox'/>&nbsp;&nbsp;Mes fichiers</h5>";
                    while ($rows = $my_files->fetch()) {
                        $file_ext = strrchr($rows['file_name'], '.');
                        $a = $rows['file_url'];
                        $b = $rows['file_name'];
                        $name_not_extension = strstr($b, '.', true);
                        if (in_array($file_ext, $extension_img)) {
                            if ($file_ext == '.txt') {
                                echo "<div class='img_legend'>";
                                echo "<img class='img' src='img/txt.png' alt=" . $rows['file_name'] . ">";
                                echo "<p class='file_name'>" . $rows['file_name'] . "</p>
                                <span class='date'>" . $rows['date'] . "</span>
                                <label for='$b'></label>
                                <a href='" . $rows['file_url'] . "' download title=\"Télécharger\"><br>
                                    <img class='logo_param' id='$b' src='img/download.png' alt='download'/>
                                 </a>
                                <label for='$a'><img class='logo_param' src='img/delete.png' alt='delete'/></label>";
                                echo "<form method='POST'>
                                        <input class='input_delete_not_visible' type='text' name='file_name_to_delete' value='$a'>
                                        <input type='submit' class='delete' id='$a' name='delete_file' value='Supprimer'>
                                    </form>";
                                echo "<div class='rename_box'>";
                                echo "<form method='POST'>
                                                    <input  class='input_rename_not_visible' type='text' name='file_url' value='$a'>
                                                    <input  class='input_rename_not_visible' type='text' name='file_name' value='$b'>
                                                    <input  type='text' class='new_name_file' placeholder='Nouveau nom' name='new_name'>
                                                    <input type='submit' name='rename_file' value='Renommer'>
                                                   </form>";
                                echo "</div>";
                                echo "</div>";
                            }else if($file_ext == '.pdf'){
                                echo "<div class='img_legend'>";
                                echo "<img class='img' src='img/pdf.png' alt=" . $rows['file_name'] . ">";
                                echo "<p class='file_name'>" . $rows['file_name'] . "</p>
                                <span class='date'>" . $rows['date'] . "</span>
                                <label for='$b'></label>
                                <a href='" . $rows['file_url'] . "' download title=\"Télécharger\"><br>
                                    <img class='logo_param' id='$b' src='img/download.png' alt='download'/>
                                 </a>
                                <label for='$a'><img class='logo_param' src='img/delete.png' alt='delete'/></label>";
                                echo "<form method='POST'>
                                        <input class='input_delete_not_visible' type='text' name='file_name_to_delete' value='$a'>
                                        <input type='submit' class='delete' id='$a' name='delete_file' value='Supprimer'>
                                    </form>";
                                echo "<div class='rename_box'>";
                                echo "<form method='POST'>
                                                    <input  class='input_rename_not_visible' type='text' name='file_url' value='$a'>
                                                    <input  class='input_rename_not_visible' type='text' name='file_name' value='$b'>
                                                    <input  type='text' class='new_name_file' placeholder='Nouveau nom' name='new_name'>
                                                    <input type='submit' name='rename_file' value='Renommer'>
                                                   </form>";
                                echo "</div>";
                                echo "</div>";
                            }else {
                                echo "<div class='img_legend'>";
                                echo "<img class='img' src=" . $rows['file_url'] . " alt=" . $rows['file_name'] . ">";
                                echo "<p class='file_name'>" . $rows['file_name'] . "</p>
                            <span class='date'>" . $rows['date'] . "</span>
                             <label for='$b'></label>
                            <a href='" . $rows['file_url'] . "' download title=\"Télécharger\"><br>
                                <img class='logo_param' id='$b' src='img/download.png' alt='download'/>
                             </a>
                             <label for='$a'><img class='logo_param' src='img/delete.png' title=\"Supprimer\" alt='delete'/></label>
                            <form method='POST'>
                                        <input class='input_delete_not_visible' type='text' name='file_name_to_delete' value='$a'>
                                        <input type='submit' class='delete' id='$a' name='delete_file' value='Supprimer'>
                                  </form>";
                                echo "<div class='rename_box'>";
                                echo "<form method='POST'>
                                        <input class='input_rename_not_visible' type='text' name='file_url' value='$a'>
                                        <input class='input_rename_not_visible' type='text' name='file_name' value='$b'>
                                        <input  type='text' class='new_name_file' placeholder='Nouveau nom' name='new_name'>
                                        <input type='submit' name='rename_file' value='Renommer'>
                                       </form>";
                                echo "</div>";
                                echo "</div>";
                            }
                        }

                    }
                }
            ?>
        </div>
        <?php include("aside.php"); ?>
    </div>
</div>
</body>

</html>
