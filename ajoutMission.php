<?php
if (isset($_GET["ville"])&&isset($_GET["dateDebut"])&&isset($_GET["dateFin"])&&isset($_GET["employe"])){
    //echo "test".$_GET["employe"].".".$_GET["ville"].".".$_GET["dateDebut"].".".$_GET["dateFin"].".";
    $pdo = new PDO('mysql:host=localhost;dbname=epoka_missions', 'root', '');
    $query = $pdo->prepare("INSERT INTO mission (Mis_NoSalarie, Mis_NoVille, Mis_DateDebut, Mis_DateFin, Mis_Validee, Mis_Remboursee) VALUES (:salarie, :ville, :dateDebut, :dateFin, false, false)");
    $query->bindValue(':salarie', $_GET["employe"]);
    $query->bindValue(':ville', $_GET["ville"]);
    $query->bindValue(':dateDebut', $_GET["dateDebut"]);
    $query->bindValue(':dateFin', $_GET["dateFin"]);
    $query->execute();
    if($query->errorInfo()[1]!=null){
        echo "error";
    } else {
        echo "success";
    }
} else {
    echo "erreur";
}