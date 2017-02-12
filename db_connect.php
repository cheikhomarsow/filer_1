<?php
try
{
    $dbh = new PDO('mysql:host=localhost;dbname=filer', 'root', '');
}
catch (Exception $e)
{
    die('Erreur : ' . $e->getMessage());
}
?>