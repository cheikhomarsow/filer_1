<?php
session_start();
$online = false;
if(empty($_SESSION['username'])) {
    header('Location:auth.php');
    exit();
}else{
    $online = true;
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
            <?php
                if($online){
                    include("header_in.php");
                }
            ?>
            <div id="content_box">
                <div id="box_articles">
                    <?php
                        require_once "db_connect.php";
                        $q = "SELECT * FROM `files` ORDER BY DATE DESC";
                        $all_files = $dbh->prepare($q);
                        $all_files->execute();
                        $extension_img = array('.jpg','.jpeg','.png','.gif');
                        echo "<p class='welcome'>Bienvenue <b>" . $_SESSION['username'] . "</b>.<br> Rendez vous à <a href='my_files.php'>'Mes fichiers'</a> pour <b>ajouter</b>, <b>renommer</b>, <b>supprimer</b> ou <b>remplacer</b> un fichier...Merci</p>";
                        if($rows = $all_files-> rowCount() == 0){
                            echo "<div class='not_files'>
                                <h5>Soyez le premier à ajouter un fichier</h5>
                                <img src='img/first.png' alt='vide'/>
                            </div>
                        ";
                        }else {
                            $cpt = 0;
                            echo "<h5><img src='img/archives.png' alt='settings'/>&nbsp;&nbsp;Tous les fichiers</h5>";
                            while ($rows = $all_files->fetch()) {
                                $cpt++;
                                $id = $rows['id_user'];
                                $req = "SELECT `username` FROM `users` WHERE id = :id";
                                $author_file = $dbh->prepare($req);
                                $author_file->execute([
                                    "id" => $id
                                ]);
                                $rows_author = $author_file->fetch();
                                $file_ext = strrchr($rows['file_name'], '.');
                                if (in_array($file_ext, $extension_img)) {
                                    echo "<div class='img_legend'>";
                                    echo "<span class='by_user'><img src='img/user.png' alt='settings'/>&nbsp;&nbsp;<em>" . $rows_author['username'] . "</em></span>";
                                    echo "<img class='img' src=" . $rows['file_url'] . " alt=" . $rows['file_name'] . ">";
                                    echo "<p class='file_name'>" . $rows['file_name'] . "<br>
                                        <span class='date'>" . $rows['date'] . "</span>
                                        <a href='" . $rows['file_url'] . "' id='" . $cpt . "' download title=\"Télécharger\"><br>
                                            <img class='logo_param'  src='img/download.png' alt='download'/>
                                        </a>";
                                    echo "</div>";
                                } else if ($file_ext == '.pdf') {
                                    echo "<div class='img_legend'>";
                                    echo "<span class='by_user'><img src='img/user.png' alt='settings'/>&nbsp;&nbsp;<em>" . $rows_author['username'] . "</em></span>";
                                    echo "<img class='img' src='img/pdf.png'>";
                                    echo "<p class='file_name'>" . $rows['file_name'] . "<br>
                                    <span class='date'>" . $rows['date'] . "</span>
                                    
                                        <label for='" . $cpt . "'><a href='" . $rows['file_url'] . "' download title=\"Télécharger\"><br>
                                            <img class='logo_param' id='" . $cpt . "' src='img/download.png' alt='download'/>
                                        </a></label>";
                                    echo "</div>";
                                } else {
                                    echo "<div class='img_legend'>";
                                    echo "<span class='by_user'><img src='img/user.png' alt='settings'/>&nbsp;&nbsp;<em>" . $rows_author['username'] . "</em></span>";
                                    echo "<img class='img' src='img/txt.png'>";
                                    echo "<p class='file_name'>" . $rows['file_name'] . "<br>
                                    <span class='date'>" . $rows['date'] . "</span>
                                    
                                        <label for='" . $cpt . "'><a href='" . $rows['file_url'] . "' download title=\"Télécharger\"><br>
                                            <img class='logo_param' id='" . $cpt . "' src='img/download.png' alt='download'/>
                                        </a></label>";
                                    echo "</div>";
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
