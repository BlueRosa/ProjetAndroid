<?php
if (isset($_GET['login'])&&isset($_GET['mdp'])) {
    $pdo = new PDO('mysql:host=localhost;dbname=epoka_missions', 'root', '');
    $query = $pdo->prepare("SELECT * FROM salarie WHERE Sal_No = :login AND Sal_MotDePasse = :mdp");
    $query->bindParam(':login', $_GET['login']);
    $query->bindParam(':mdp', $_GET['mdp']);
    $query->execute();
    if ($query->rowCount() > 0) {
        echo JSON_encode($query->fetchAll(PDO::FETCH_ASSOC));
    } else {
        echo "not connected";
    }
}
